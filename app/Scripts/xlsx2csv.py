import sys
import openpyxl
import csv

def main():
    if len(sys.argv) < 3:
        print("Usage: xlsx2csv.py <input_xlsx> <output_csv>")
        sys.exit(1)
        
    input_file = sys.argv[1]
    output_file = sys.argv[2]
    
    try:
        wb = openpyxl.load_workbook(input_file, read_only=True, data_only=True)
        ws = wb.active
        
        with open(output_file, 'w', encoding='utf-8', newline='') as f:
            writer = csv.writer(f)
            for row in ws.iter_rows(values_only=True):
                # Convert none values to empty strings
                row_values = [str(cell) if cell is not None else '' for cell in row]
                # Skip completely empty rows
                if not any(val.strip() for val in row_values):
                    continue
                writer.writerow(row_values)
        print("Success")
    except Exception as e:
        print(f"Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()
