<?php

namespace App\Constants\TemplateConstant;

use App\Constants\BaseConstant;

class DefaultTemplate extends BaseConstant
{
    const TEMPLATE_GROUPS = [
        [
            'name' => 'Advertising and Marketing',
            'templates' => [
                [
                    'name' => 'Product ads',
                    'subject' => 'Discover Our Latest Product Innovation!',
                    'content' => "<p>Hello @name@,</p><br/>
                        <p>We're thrilled to introduce our latest product that promises to revolutionize the way you [benefit]. Our [Product Name] brings you cutting-edge [features or benefits], designed to enhance your [specific use or outcome]. It's the perfect solution for those seeking [problem-solving features].</p>
                        <br/>
                        <p>Explore [Product Name] now: [Link to Product Page]. Don't miss out on this game-changing advancement in [industry or niche]!</p>
                        <br/>
                        <p>Best regards,</p>
                        <p>@enterprise@ Team</p>",
                ],
                [
                    'name' => 'Discounts/Promotions',
                    'subject' => 'Exclusive Offer Inside - Limited Time Only!',
                    'content' => "<p>Hello @name@,</p><br/>
                        <p>Great news! We're offering an exclusive [discount/offer] on our premium [Product/Service] just for you! Avail yourself of this limited-time deal to [benefit from features/offers]. Use code '[Discount Code]' at checkout to claim your discount.</p>
                        <br/>
                        <p>Hurry, this offer expires [expiry date]. Grab the opportunity now: [Link to Offer Page]!</p>
                        <br/>
                        <p>Warm regards,</p>
                        <p>@enterprise@ Team</p>",
                ],
                [
                    'name' => 'Events/Courses',
                    'subject' => "You're Invited! Join Us for an Exciting Event/Course!",
                    'content' => "<p>Dear @name@,</p><br/>
                        <p>We're thrilled to invite you to [Event/Course Name], an exclusive event/course designed to [purpose or benefits]. Join us on [Date & Time] to explore the latest trends in [industry/topic] and network with industry experts.</p>
                        <br/>
                        <p>Secure your spot now and be part of this enlightening experience: [Event/Course Registration Link].</p>
                        <br/>
                        <p>Looking forward to seeing you there!</p>
                        <p>Best regards,</p>
                        <p>@enterprise@ Team</p>",
                ],
            ],
        ],
        [
            'name' => 'Customer care',
            'templates' => [
                [
                    'name' => 'Welcome new members',
                    'subject' => 'Discover Our Latest Product Innovation!',
                    'content' => "<p>Welcome aboard, @name@!</p><br/>
                        <p>We're excited to have you join our community. At [Your Company], we strive to provide you with [value proposition]. Get started by exploring our platform and discovering [features/benefits] designed especially for you.</p>
                        <br/>
                        <p>Feel free to reach out if you have any questions or need assistance. We're here to make your experience delightful!</p>
                        <br/>
                        <p>Best regards,</p>
                        <p>@enterprise@ Team</p>",
                ],
                [
                    'name' => 'Customer Inquiries/Surveys',
                    'subject' => 'Your Opinion Matters - Help Us Serve You Better!',
                    'content' => '<p>Hello @name@,</p><br/>
                        <p>We value your feedback to enhance our services/products. Your opinion matters! Please take a moment to share your thoughts through this quick survey: [Survey Link]. Your input will aid us in improving and delivering a better experience tailored to your needs.</p>
                        <br/>
                        <p>Thank you for being a part of our journey!</p>
                        <br/>
                        <p>Warm regards,</p>
                        <p>@enterprise@ Team</p>',
                ],
                [
                    'name' => 'Problem Notification / Problem Resolution Email',
                    'subject' => 'Important Update Regarding [Issue] - Resolved!',
                    'content' => "<p>Dear @name@,</p><br/>
                        <p>We want to inform you that the issue you reported regarding [description of issue] has been successfully resolved. Our team worked diligently to fix this, ensuring a seamless experience for you moving forward.</p>
                        <br/>
                        <p>We apologize for any inconvenience caused and appreciate your patience. Should you have any further concerns, please don't hesitate to reach out.</p>
                        <br/>
                        <p>Best regards,</p>
                        <p>@enterprise@ Team</p>",
                ],
            ],
        ],
        [
            'name' => 'Information and News',
            'templates' => [
                [
                    'name' => 'Weekly / Monthly Newsletter',
                    'subject' => 'Stay Updated with Our Latest News and Offers!',
                    'content' => "<p>Hello, @name@!</p><br/>
                        <p>Welcome to our weekly/monthly newsletter! Get ready to dive into the latest news, trends, and exclusive offers in [industry/niche]. We've curated this edition just for you, packed with valuable insights and exciting updates.</p>
                        <br/>
                        <p>Explore our newsletter here: [Link to Newsletter]. Don't miss out!</p>
                        <br/>
                        <p>Best regards,</p>
                        <p>@enterprise@ Team</p>",
                ],
                [
                    'name' => 'Product / Service Update Email',
                    'subject' => "Discover What's New with [Your Product/Service]!",
                    'content' => "<p>Hello @name@,</p><br/>
                        <p>We're excited to share the latest updates on [Your Product/Service]. Discover our newest features, enhancements, and offerings that will elevate your experience. Stay ahead with what's fresh and innovative!</p>
                        <br/>
                        <p>Explore now: [Link to Updates].</p>
                        <br/>
                        <p>Warm regards,</p>
                        <p>@enterprise@ Team</p>",
                ],
                [
                    'name' => 'Document / Article Sharing Email',
                    'subject' => 'Engage with Our Latest Documents and Articles!',
                    'content' => "<p>Dear @name@,</p><br/>
                        <p>We're thrilled to present our latest collection of insightful documents and engaging articles. Dive into a wealth of knowledge and thought-provoking content that brings value to your interests.</p>
                        <br/>
                        <p>Explore our library: [Link to Documents/Articles].</p>
                        <br/>
                        <p>Best regards,</p>
                        <p>@enterprise@ Team</p>",
                ],
            ],
        ],
        [
            'name' => 'Order and Payment',
            'templates' => [
                [
                    'name' => 'Order Confirmation Email',
                    'subject' => 'Your Order with @enterprise@ - Confirmed!',
                    'content' => "<p>Dear Customer,</p>
                        <p>Thank you for choosing @enterprise@! Your order has been successfully placed. Here are the details:</p>
                        <ul>
                            <li><strong>Order ID:</strong> [Order ID]</li>
                            <li><strong>Items:</strong> [List of Items]</li>
                            <li><strong>Total Amount:</strong> [Total Amount]</li>
                        </ul>
                        <p>We'll keep you updated on the delivery status. Should you have any queries, feel free to reach out.</p>
                        <p>Warm regards,<br>@enterprise@ Team</p>",
                ],
                [
                    'name' => 'Successful Payment Notification',
                    'subject' => 'Payment Confirmation - Transaction Complete!',
                    'content' => '<p>Hi,</p>
                        <p>Your recent payment has been successfully processed. Here are the transaction details:</p>
                        <ul>
                            <li><strong>Transaction ID:</strong> [Transaction ID]</li>
                            <li><strong>Amount:</strong> [Amount]</li>
                            <li><strong>Date:</strong> [Date]</li>
                        </ul>
                        <p>Thank you for your prompt payment. For any payment-related inquiries, please contact us.</p>
                        <p>Best regards,<br>@enterprise@ Team</p>',
                ],
                [
                    'name' => 'Payment Reminder Email',
                    'subject' => 'Friendly Reminder - Payment Due Soon!',
                    'content' => '<p>Hello @name@,</p>
                        <p>This is a gentle reminder that your payment for [Service/Product] is due soon. Ensure timely payment to avoid any inconvenience. Here are the payment details:</p>
                        <ul>
                            <li><strong>Amount Due:</strong> [Amount]</li>
                            <li><strong>Due Date:</strong> [Due Date]</li>
                        </ul>
                        <p>Make a hassle-free payment now or reach out if you need any assistance.</p>
                        <p>Warm regards,<br>@enterprise@ Team</p>',
                ],
            ],
        ],
    ];
}
