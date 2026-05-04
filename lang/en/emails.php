<?php

return [
    'order' => [
        'subject' => 'Order Confirmation #:id',
        'heading' => 'Order confirmation',
        'greeting' => 'Hi :name,',
        'intro' => 'Thanks for your order. Your order number is :id.',
        'table' => [
            'product' => 'Product',
            'quantity' => 'Qty',
            'price' => 'Price',
            'total' => 'Total',
        ],
        'product_fallback' => 'Product',
        'total' => 'Order total',
        'view_order' => 'View order',
        'attachment_notice' => 'Your invoice is attached as a PDF when it is available.',
        'thanks' => 'Thanks',
    ],
    'admin' => [
        'low_stock_subject' => 'Low-stock alert',
        'low_stock_heading' => 'Low-stock alert',
        'low_stock_intro' => 'The following products are at or below the low-stock threshold of :threshold units.',
        'low_stock_footer' => 'Please review inventory and restock as needed.',
        'table' => [
            'product' => 'Product',
            'stock' => 'Stock',
        ],
    ],
];
