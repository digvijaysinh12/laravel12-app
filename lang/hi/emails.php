<?php

return [
    'order' => [
        'subject' => 'ऑर्डर पुष्टि #:id',
        'heading' => 'ऑर्डर पुष्टि',
        'greeting' => 'नमस्ते :name,',
        'intro' => 'आपके ऑर्डर के लिए धन्यवाद। आपका ऑर्डर नंबर :id है।',
        'table' => [
            'product' => 'उत्पाद',
            'quantity' => 'मात्रा',
            'price' => 'कीमत',
            'total' => 'कुल',
        ],
        'product_fallback' => 'उत्पाद',
        'total' => 'ऑर्डर कुल',
        'view_order' => 'ऑर्डर देखें',
        'attachment_notice' => 'जब उपलब्ध होगा, आपका इनवॉइस PDF के रूप में संलग्न होगा।',
        'thanks' => 'धन्यवाद',
    ],
    'admin' => [
        'low_stock_subject' => 'कम स्टॉक चेतावनी',
        'low_stock_heading' => 'कम स्टॉक चेतावनी',
        'low_stock_intro' => 'निम्नलिखित उत्पाद :threshold यूनिट की कम-स्टॉक सीमा पर या उससे नीचे हैं।',
        'low_stock_footer' => 'कृपया इन्वेंटरी की समीक्षा करें और आवश्यक होने पर रीस्टॉक करें।',
        'table' => [
            'product' => 'उत्पाद',
            'stock' => 'स्टॉक',
        ],
    ],
];
