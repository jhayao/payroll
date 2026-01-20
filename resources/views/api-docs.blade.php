<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timekeeper API Documentation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            border-bottom: 2px solid #eaecef;
            padding-bottom: 0.3em;
        }

        h2 {
            border-bottom: 1px solid #eaecef;
            padding-bottom: 0.3em;
            margin-top: 24px;
        }

        pre {
            background-color: #f6f8fa;
            border-radius: 6px;
            padding: 16px;
            overflow: auto;
        }

        code {
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
            font-size: 85%;
            background-color: rgba(27, 31, 35, 0.05);
            padding: 0.2em 0.4em;
            border-radius: 3px;
        }

        pre code {
            background-color: transparent;
            padding: 0;
        }

        .endpoint {
            font-weight: bold;
            color: #0366d6;
        }

        .method {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            font-size: 0.8em;
            margin-right: 8px;
        }

        .post {
            background-color: #28a745;
        }

        .get {
            background-color: #0366d6;
        }

        hr {
            height: 0.25em;
            padding: 0;
            margin: 24px 0;
            background-color: #e1e4e8;
            border: 0;
        }
    </style>
</head>

<body>

    <h1>Timekeeper API Documentation</h1>
    <p>This document outlines the API endpoints available for Timekeepers to manage their projects, employees, and
        attendance.</p>

    <h3>Base URL</h3>
    <p>All requests should be prefixed with your API host (e.g., <code>{{ url('/api') }}</code>).</p>

    <hr>

    <h2>1. Login / Verification</h2>

    <p><span class="method post">POST</span> <span class="endpoint">/timekeeper-login</span></p>

    <p>Use this endpoint to verify credentials and retrieve the <strong>Timekeeper ID</strong>
        (<code>employee_id</code>) needed for subsequent requests.</p>

    <p><strong>Parameters:</strong></p>
    <ul>
        <li><code>email</code>: (string) Required.</li>
        <li><code>password</code>: (string) Required.</li>
    </ul>

    <p><strong>Response:</strong></p>
    <pre><code>{
    "status": "success",
    "message": "Timekeeper verified successfully.",
    "user": {
        "id": 1,
        "employee_id": 123456, // &lt;--- USE THIS AS timekeeper_id
        "name": "John Doe",
        "email": "john@example.com",
        "role": "timekeeper"
    }
}
</code></pre>

    <hr>

    <h2>2. Get Projects</h2>

    <p><span class="method get">GET</span> <span class="endpoint">/timekeeper/projects</span></p>

    <p>List all projects managed by the authenticated timekeeper.</p>

    <p><strong>Parameters:</strong></p>
    <ul>
        <li><code>timekeeper_id</code>: (integer) Required. The <code>employee_id</code> from the login response.</li>
    </ul>

    <p><strong>Response:</strong></p>
    <pre><code>[
    {
        "id": 5,
        "name": "Bridge Construction Phase 1",
        "description": "Main bridge framework",
        "status": "ongoing",
        "start_date": "2025-01-01",
        "end_date": "2025-06-30"
    }
]
</code></pre>

    <hr>

    <h2>3. Get Employees</h2>

    <p><span class="method get">GET</span> <span class="endpoint">/timekeeper/employees</span></p>

    <p>List all employees assigned to the timekeeper's projects. Returns unique employees even if they are in multiple
        projects.</p>

    <p><strong>Parameters:</strong></p>
    <ul>
        <li><code>timekeeper_id</code>: (integer) Required.</li>
        <li><code>project_id</code>: (integer) Optional. If provided, filters employees by a specific project.</li>
    </ul>

    <p><strong>Response:</strong></p>
    <pre><code>[
    {
        "id": 472218,
        "name": "Dela Cruz, Juan M",
        "photo_url": "http://domain.com/images/uploads/2x2/472218.jpg",
        "position": "Construction Worker",
        "face_photos": [
            "http://domain.com/images/uploads/lg/1.jpg",
            "http://domain.com/images/uploads/lg2/1.jpg",
            "http://domain.com/images/uploads/lg3/1.jpg"
        ]
    }
]
</code></pre>

    <hr>

    <h2>4. Get Attendance (DTR)</h2>

    <p><span class="method get">GET</span> <span class="endpoint">/timekeeper/attendance</span></p>

    <p>Fetch Daily Time Records (DTR) for the managed employees within a specific date range.</p>

    <p><strong>Parameters:</strong></p>
    <ul>
        <li><code>timekeeper_id</code>: (integer) Required.</li>
        <li><code>date_from</code>: (date, YYYY-MM-DD) Required.</li>
        <li><code>date_to</code>: (date, YYYY-MM-DD) Required.</li>
        <li><code>project_id</code>: (integer) Optional. Filter by project scope (though DTR is per employee).</li>
    </ul>

    <p><strong>Response:</strong></p>
    <pre><code>[
    {
        "id": 50,
        "employee_id": 472218,
        "employee_name": "Dela Cruz, Juan M",
        "log_date": "2025-01-20",
        "am_in": "08:00:00",
        "am_out": "12:00:00",
        "pm_in": "13:00:00",
        "pm_out": "17:00:00",
        "ot_in": null,
        "ot_out": null
    }
]
</code></pre>

</body>

</html>