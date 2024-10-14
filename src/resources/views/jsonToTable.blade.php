<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Json to table</title>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    .nested-table {
        margin-left: 20px;
    }
</style>
</head>
<body>

<h2>Visualizador de bitacora</h2>

<label for="jsonUrl">Ingresa la URL:</label>
<input type="text" id="jsonUrl" placeholder="Enter URL...">
<button onclick="generateTable()">Generate Table</button>

<div id="jsonTable"></div>

<script>
    // Función para hacer una solicitud HTTP y procesar el JSON
    async function fetchAndDisplayJSON(url) {
        try {
            const response = await fetch(url);
            const data = await response.json();
            displayJSONAsTable(data);
        } catch (error) {
            console.error('Error fetching JSON:', error);
        }
    }

    // Función para generar la tabla HTML
    function displayJSONAsTable(data, parentElement = document.getElementById('jsonTable')) {
        const table = document.createElement('table');

        // Crear encabezados de tabla si el objeto es un array
        if (Array.isArray(data) && data.length > 0) {
            const headers = Object.keys(data[0]);
            const headerRow = document.createElement('tr');
            headers.forEach(headerText => {
                const th = document.createElement('th');
                th.textContent = headerText;
                headerRow.appendChild(th);
            });
            table.appendChild(headerRow);
        }

        // Crear filas de datos
        data.forEach(obj => {
            const row = document.createElement('tr');
            Object.values(obj).forEach(value => {
                const cell = document.createElement('td');
                if (typeof value === 'object') {
                    // Si el valor es un objeto, renderizar una tabla anidada
                    const nestedTable = document.createElement('table');
                    displayJSONAsTable(value, nestedTable);
                    cell.appendChild(nestedTable);
                } else {
                    cell.textContent = value;
                }
                row.appendChild(cell);
            });
            table.appendChild(row);
        });

        // Agregar la tabla al elemento padre
        parentElement.appendChild(table);
    }


    // Función para generar la tabla cuando se hace clic en el botón
    function generateTable() {
        const jsonUrlInput = document.getElementById('jsonUrl');
        const url = jsonUrlInput.value.trim();
        if (url !== '') {
            fetchAndDisplayJSON(url);
        }
    }
</script>

</body>
</html>