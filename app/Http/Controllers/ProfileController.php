<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\PatientUserLink;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $linkFeatureAvailable = Schema::hasTable('patient_user_links');
        $linkedPatients = collect();
        $pendingPatientLinks = collect();
        $matchingPatients = collect();
        $medicalRecords = collect();
        $recentPrescriptions = collect();

        if ($linkFeatureAvailable) {
            $verifiedPatientIds = PatientUserLink::query()
                ->where('user_id', $user->id)
                ->when(Schema::hasColumn('patient_user_links', 'is_verified'), fn ($query) => $query->where('is_verified', true))
                ->pluck('patient_id');

            $directPatientIds = Patient::query()
                ->where('user_id', $user->id)
                ->pluck('id');

            $linkedPatientIds = $verifiedPatientIds
                ->merge($directPatientIds)
                ->unique()
                ->values();

            if ($linkedPatientIds->isNotEmpty()) {
                $linkedPatients = Patient::query()
                    ->whereIn('id', $linkedPatientIds)
                    ->orderBy('full_name')
                    ->get();

                $medicalRecords = MedicalRecord::with(['patient', 'staff'])
                    ->whereIn('patient_id', $linkedPatientIds)
                    ->orderByDesc('visit_date')
                    ->orderByDesc('id')
                    ->take(5)
                    ->get();

                $recentPrescriptions = Prescription::with(['medicalRecord.patient', 'items.inventoryItem', 'items.medicinalHerb', 'items.packagedProduct'])
                    ->whereHas('medicalRecord', fn ($query) => $query->whereIn('patient_id', $linkedPatientIds))
                    ->whereIn('status', ['confirmed', 'dispensed'])
                    ->orderByDesc('created_at')
                    ->take(5)
                    ->get();
            }

            if (Schema::hasColumn('patient_user_links', 'is_verified')) {
                $pendingPatientLinks = PatientUserLink::with('patient')
                    ->where('user_id', $user->id)
                    ->where('is_verified', false)
                    ->latest()
                    ->get();
            }

            $existingLinkedIds = PatientUserLink::where('user_id', $user->id)->pluck('patient_id')
                ->merge(Patient::where('user_id', $user->id)->pluck('id'))
                ->unique();

            $matchingPatients = $this->findPatientsByUserPhone($user->phone, $existingLinkedIds);
        }

        return view('user.dashboard', compact(
            'linkFeatureAvailable',
            'linkedPatients',
            'pendingPatientLinks',
            'matchingPatients',
            'medicalRecords',
            'recentPrescriptions'
        ));
    }

    public function edit()
    {
        return redirect()->route('dashboard')->with('open_profile', true);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return back()->with('status', 'Thông tin cá nhân đã được cập nhật.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Mật khẩu đã được thay đổi.');
    }

    public function favorites()
    {
        $user = auth()->user();
        $favHerbs = $user->herbDictionaryFavorites()->get();
        $favArticles = $user->likedArticles()->get();

        return view('user.favorites', compact('user', 'favHerbs', 'favArticles'));
    }

    public function requestPatientLink(Request $request)
    {
        abort_unless(Schema::hasTable('patient_user_links'), 404);

        $validated = $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
        ]);

        $user = auth()->user();
        $userPhone = $this->normalizePhone($user->phone);

        if (!$userPhone) {
            return back()->with('error', 'Vui lòng cập nhật số điện thoại trước khi đồng bộ hồ sơ khám bệnh.');
        }

        $patient = Patient::findOrFail($validated['patient_id']);
        $patientPhone = $this->normalizePhone($patient->phone);
        $guardianPhone = $this->normalizePhone($patient->guardian_phone);

        $matchesPatientPhone = $patientPhone && $patientPhone === $userPhone;
        $matchesGuardianPhone = $guardianPhone && $guardianPhone === $userPhone;

        if (!$matchesPatientPhone && !$matchesGuardianPhone) {
            return back()->with('error', 'Số điện thoại tài khoản không khớp với hồ sơ bệnh nhân đã chọn.');
        }

        if ((int) $patient->user_id === (int) $user->id) {
            return back()->with('status', 'Hồ sơ này đã được liên kết với tài khoản của bạn.');
        }

        $matchingPatients = $this->findPatientsByUserPhone($user->phone, collect());
        $canAutoVerify = $matchesPatientPhone && $matchingPatients->count() === 1 && Schema::hasColumn('patient_user_links', 'is_verified');
        $relationshipColumn = Schema::hasColumn('patient_user_links', 'relationship_type') ? 'relationship_type' : 'relation_type';

        $payload = [
            $relationshipColumn => $matchesPatientPhone ? 'self' : 'guardian',
        ];

        if (Schema::hasColumn('patient_user_links', 'is_verified')) {
            $payload['is_verified'] = $canAutoVerify;
        }

        PatientUserLink::updateOrCreate(
            [
                'user_id' => $user->id,
                'patient_id' => $patient->id,
            ],
            $payload
        );

        if ($canAutoVerify) {
            return back()->with('status', 'Đã đồng bộ hồ sơ khám bệnh với tài khoản của bạn.');
        }

        return back()->with('status', 'Đã gửi yêu cầu liên kết hồ sơ. Nhà thuốc sẽ xác minh trước khi hiển thị lịch sử khám và toa thuốc.');
    }

    private function findPatientsByUserPhone(?string $phone, Collection $excludedPatientIds): Collection
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if (!$normalizedPhone) {
            return collect();
        }

        return Patient::query()
            ->where(function ($query) {
                $query->whereNotNull('phone')
                    ->orWhereNotNull('guardian_phone');
            })
            ->orderByDesc('updated_at')
            ->get()
            ->filter(function (Patient $patient) use ($normalizedPhone, $excludedPatientIds) {
                if ($excludedPatientIds->contains($patient->id)) {
                    return false;
                }

                return $this->normalizePhone($patient->phone) === $normalizedPhone
                    || $this->normalizePhone($patient->guardian_phone) === $normalizedPhone;
            })
            ->take(5)
            ->values()
            ->map(function (Patient $patient) use ($normalizedPhone) {
                $patient->masked_name = $this->maskPatientName($patient->full_name);
                $patient->matched_relation_label = $this->normalizePhone($patient->phone) === $normalizedPhone
                    ? 'Số bệnh nhân'
                    : 'Số người giám hộ';

                return $patient;
            });
    }

    private function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '84') && strlen($digits) >= 10) {
            $digits = '0' . substr($digits, 2);
        }

        return $digits;
    }

    private function maskPatientName(?string $name): string
    {
        $name = trim((string) $name);

        if ($name === '') {
            return 'Bệnh nhân';
        }

        $parts = preg_split('/\s+/u', $name) ?: [];

        if (count($parts) <= 1) {
            return mb_substr($name, 0, 1) . '...';
        }

        $last = array_pop($parts);
        $maskedLast = mb_substr($last, 0, 1) . str_repeat('*', max(1, mb_strlen($last) - 1));

        return implode(' ', $parts) . ' ' . $maskedLast;
    }
}
