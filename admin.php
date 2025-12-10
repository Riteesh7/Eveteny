<?php /* Event Organizer Dashboard for managing tickets */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Organizer Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Organizer Dashboard</h1>
            <button id="create-ticket-btn" class="btn btn-primary">+ Create Ticket</button>
        </header>

        <div class="card">
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Visibility</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="admin-ticket-list">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="ticket-modal" class="modal">
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <h2 id="modal-title" style="margin-bottom: 1.5rem;">Create Ticket</h2>
            
            <form id="ticket-form" enctype="multipart/form-data">
                <input type="hidden" id="ticket-id" name="id">
                
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="quantity" name="quantity" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Sale Start</label>
                        <input type="datetime-local" id="sale_start" name="sale_start" required>
                    </div>
                    <div class="form-group">
                        <label>Sale End</label>
                        <input type="datetime-local" id="sale_end" name="sale_end" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Visibility</label>
                    <select id="is_public" name="is_public">
                        <option value="1">Public</option>
                        <option value="0">Private</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Image (Optional)</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Ticket</button>
            </form>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
