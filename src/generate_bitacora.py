import json
from json2table import convert

def json_to_html(json_file_path, html_file_path):
    """
    Reads a JSON file, converts it to an HTML table, and saves the output.

    Args:
        json_file_path (str): Path to the JSON file.
        html_file_path (str): Path to save the generated HTML file.
    """

    try:
        # Read JSON file
        with open(json_file_path, 'r') as f:
            json_data = json.load(f)

        # Create an empty list to store HTML content
        html_content = []

        # Iterate over each dictionary in the list
        for item in json_data:
            # Convert each dictionary to HTML table
            html = convert(item)
            # Append the HTML content to the list
            html_content.append(html)

        # Write the combined HTML content to the file
        with open(html_file_path, 'w') as f:
            f.writelines(html_content)

        print(f"Successfully converted '{json_file_path}' to '{html_file_path}'.")

    except FileNotFoundError:
        print(f"Error: File '{json_file_path}' not found.")
    except Exception as e:
        print(f"An error occurred: {e}")

# Replace with your actual JSON and HTML file paths
json_file_path = "/home/server/Documents/manejador_correos/MANEJADOR_PREMIUM/src/public/BIT_PREMIUM/2024-03-05.json"
html_file_path = "/home/server/Documents/manejador_correos/MANEJADOR_PREMIUM/src/public/BIT_PREMIUM/2024-03-05.html"

json_to_html(json_file_path, html_file_path)