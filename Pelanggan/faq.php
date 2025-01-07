<!DOCTYPE html>
<html lang="en">
<head>
    <link href="../Pelanggan/assets/css/main.css?v=1.1" rel="stylesheet" type="text/css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            margin: 0;
            padding: 0;
        }

        .content-main {
            width: 100%;
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .content-header h2 {
            text-align: center;
            color: black;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        details {
            width: 100%;
            margin-bottom: 1.5em;
            border: 1px solid #007BFF;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.1);
        }

        details:hover {
            box-shadow: 0 6px 12px rgba(0, 123, 255, 0.2);
        }

        summary {
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            background: #007BFF;
            color: #fff;
            padding: 15px;
            margin: -1px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        summary:hover {
            background: #0056b3;
        }

        summary::before {
            content: "+";
            font-size: 1.2rem;
            margin-right: 10px;
            color: #fff;
        }

        details[open] summary::before {
            content: "-";
        }

        .text-muted {
            font-size: 14px;
            color: #6c757d;
            padding: 15px;
            background: #f8f9fa;
        }

        .contact-section {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }

        .contact-section a {
            color: #007BFF;
            text-decoration: none;
        }

        .contact-section a:hover {
            text-decoration: underline;
        }

        /* Responsiveness */
        @media (max-width: 1080px) {
            .content-main {
                max-width: 90%;
                padding: 15px;
            }

            summary {
                font-size: 1rem;
                padding: 12px;
            }

            .text-muted {
                font-size: 13px;
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .content-header h2 {
                font-size: 1.5rem;
            }

            summary {
                font-size: 0.9rem;
                padding: 10px;
            }

            .text-muted {
                font-size: 12px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <section class="content-main">
        <div class="content-header">
            <h2 class="content-title">Frequently Asked Questions</h2>
        </div>
        <div class="card mb-4">
            <details>
                <summary>How do I make a purchase?</summary>
                <div class="text-muted">
                    <p>To make a purchase, browse our catalog, add items to your cart, and proceed to checkout. Follow the payment instructions to complete your order.</p>
                </div>
            </details>
            <details>
                <summary>What is your return policy?</summary>
                <div class="text-muted">
                    <p>We offer a 30-day return policy. Items must be in original condition. Contact our support team for further assistance.</p>
                </div>
            </details>
            <details>
                <summary>How can I track my order?</summary>
                <div class="text-muted">
                    <p>You can track your order via the link sent to your email after purchase or by logging into your account and navigating to 'Order History'.</p>
                </div>
            </details>
            <details>
                <summary>How do I reset my password?</summary>
                <div class="text-muted">
                    <p>Click on the 'Forgot Password' link on the login page and follow the instructions to reset your password.</p>
                </div>
            </details>
        </div>
        <div class="contact-section">
            <p>If you have further questions, feel free to contact us at:</p>
            <a href="mailto:support@example.com">support@example.com</a>
        </div>
    </section>
</body>
</html>
