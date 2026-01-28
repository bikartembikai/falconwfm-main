import pandas as pd
import json
import sys

try:
    # Read Excel file
    # It might have multiple sheets. Let's read all.
    xls = pd.ExcelFile('Event and Facilitator List.xlsx')
    
    data = {}
    for sheet_name in xls.sheet_names:
        df = pd.read_excel(xls, sheet_name=sheet_name)
        # Convert timestamps to string to avoid serialization errors
        df = df.applymap(lambda x: str(x) if isinstance(x, (pd.Timestamp, pd.Timedelta)) else x)
        data[sheet_name] = df.to_dict(orient='records')

    print(json.dumps(data, indent=2))

except Exception as e:
    print(f"Error: {e}")
    sys.exit(1)

