<!DOCTYPE html>
<html lang="en">
<head>
    <link href="../Pelanggan/assets/css/main.css?v=1.1" rel="stylesheet" type="text/css" />
    <style>
        .content-main {
            padding: 20px;
        }
        .card-header h4 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .colors-whatsapp-container {
            display: flex;
            flex-direction: column;
        }

        .colors-whatsapp-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 5px;
        }

        .colors-whatsapp-row select {
            flex: 1;
            max-width: 250px; /* Adjust as needed */
        }

        .whatsapp-button {
            background-color: #25D366;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-left: 10px; /* Adds spacing between the dropdown and button */
            white-space: nowrap; /* Prevents text wrap on small screens */
        }

        .instruction-section, .additional-info-section {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #555;
        }
        .product__details__option {
            margin-top: 20px;
        }
        .product__details__option__size {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .product__details__option__size span {
            font-weight: bold;
            margin-right: 10px;
        }
        .product__details__option__size label {
            cursor: pointer;
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }
        .product__details__option__size label.active, 
        .product__details__option__size label:hover {
            background-color: #333;
            color: #fff;
        }
        .product__details__option__size input[type="radio"] {
            display: none;
        }
        .product__details__cart__option {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
            font-weight: bold;
        }
        .quantity input[type="number"] {
            width: 50px;
            padding: 5px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .purchase-button {
            padding: 10px 20px;
            background-color: #000;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .purchase-button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <section class="content-main">
        <div class="content-header">
            <h2 class="content-title">Customization</h2>
        </div>
        
        <!-- Card for Design Upload Form -->
        <div class="card mb-4">
            <header class="card-header">
                <h4>Upload Design for Customization</h4>
            </header>
            <div class="card-body">
                <h5 class="card-title">Upload Design Image</h5>
                <form>
                    <div class="form-group">
                        <label for="design-image">Design Image</label>
                        <input type="file" id="design-image" name="design-image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" placeholder="Enter design description here"></textarea>
                    </div>
                    
                    <!-- Updated Shirt Size Section -->
                    <div class="product__details__option">
                        <div class="product__details__option__size">
                            <span>Shirt Size:</span>
                            <label for="xxl">XXL
                                <input type="radio" id="xxl" name="size" value="XXL">
                            </label>
                            <label for="xl">XL
                                <input type="radio" id="xl" name="size" value="XL">
                            </label>
                            <label for="l">L
                                <input type="radio" id="l" name="size" value="L">
                            </label>
                            <label for="m">M
                                <input type="radio" id="m" name="size" value="M">
                            </label>
                            <label for="s">S
                                <input type="radio" id="s" name="size" value="S">
                            </label>
                        </div>
                    </div>
                    
                    <!-- Updated Quantity Section -->
                    <div class="product__details__cart__option">
                        <span>Quantity:</span>
                        <div class="quantity">
                            <input type="number" id="quantity" name="quantity" value="1" min="1" placeholder="Enter quantity">
                        </div>
                    </div>

                    <div class="form-group colors-whatsapp-container">
                        <label for="color-choice">Number of Colors (Design/Print)</label>
                        <div class="colors-whatsapp-row">
                            <select id="color-choice" name="color-choice">
                                <option value="1">1 - 3 colors (Rp 25,000)</option>
                                <option value="4">4 or more colors (Rp 50,000)</option>
                            </select>
                            <a href="https://wa.me/+62895375474787" target="_blank" class="whatsapp-button">
                                Contact Seller on WhatsApp
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <!-- Card for Ordering Tutorial -->
        <div class="card mb-4 instruction-section">
            <header class="card-header">
                <h4>Ordering Tutorial</h4>
            </header>
            <div class="card-body">
                <ol>
                    <li>Select the design image and fill in the description.</li>
                    <li>Choose your preferred shirt size, quantity, and number of colors.</li>
                    <li>Contact us on WhatsApp to finalize your order and receive additional customization help.</li>
                </ol>
            </div>
        </div>

        <!-- Card for Additional Information -->
        <div class="card additional-info-section">
            <header class="card-header">
                <h4>Additional Information</h4>
            </header>
            <div class="card-body">
                <p>
                    <strong>Fabric Information:</strong><br>
                    - Shirt: Cotton combed<br>
                    - Hoodie: Cotton fleece<br>
                    - Pants: Cotton fleece<br>
                    - Socks: Free cotton<br>
                </p>
                <p><strong>Customization Fees:</strong></p>
                <ul>
                    <li>1-3 colors (Rp 25,000)</li>
                    <li>4 or more colors (Rp 50,000)</li>
                    <li>Self-designed: Free</li>
                    <li>Design by Unformal: Free</li>
                    <li>Discount: Purchase 12 or more items and get Rp 1,000 off per item.</li>
                </ul>
            </div>
        </div>
    </section>
</body>
</html>
