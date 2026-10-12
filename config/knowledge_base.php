<?php

/*
|--------------------------------------------------------------------------
| Knowledge Base sections
|--------------------------------------------------------------------------
|
| Each section is shown to exactly one audience (see
| KnowledgeBaseController::sectionFor()): Account Executives get 'ae',
| every other role gets 'general'.
|
| 'folder' is a directory under resources/knowledge-base/. Documents live
| there (not in public/) and are streamed by the knowledge-base.document
| route, which only ever reads from the signed-in user's own section.
|
| 'items' is the curated list. An entry with a 'file' key points at a
| document in the section's folder; an entry with a 'url' key is an
| external link. Entries whose file is missing, or whose url is empty
| or '#', are never shown. Any file dropped in the folder that
| isn't listed here is still picked up automatically, titled after its
| filename - list it here only to give it a nicer title/description.
|
| To add a document: copy it into the folder (optionally add an entry).
| To remove one: delete it from the folder (and its entry, if any).
|
*/

return [

    'sections' => [

        'general' => [
            'title' => 'Knowledge Base',
            'description' => 'Documents, forms and reference material for the team - all in one place.',
            'folder' => 'general',
            'items' => [
                ['file' => 'AU Savers Call & Email Script.docx', 'title' => 'AU Savers Call & Email Script', 'description' => 'Call and email script for AU Savers customers.'],
                ['file' => 'Supplier Abbreveations. 2.docx', 'title' => 'Supplier Abbreviations', 'description' => 'Reference list of supplier abbreviations.'],
                ['file' => 'BG Lite Electric n 2.pdf', 'title' => 'BG Lite Electric', 'description' => 'British Gas Lite electricity document.'],
                ['file' => 'Everflow Water LOA 1.pdf', 'title' => 'Everflow Water LOA', 'description' => 'Letter of Authority for Everflow Water.'],
                ['file' => 'Lourades Eon LOA 1.pdf', 'title' => 'E.ON LOA', 'description' => 'Letter of Authority for E.ON.'],
                ['file' => 'Lourades Eon Next Elec New Connection 1.docx', 'title' => 'E.ON Next Electricity New Connection', 'description' => 'New connection form for E.ON Next electricity supplies.'],
                ['file' => 'Lourades Eon Next Gas New Connection 1.docx', 'title' => 'E.ON Next Gas New Connection', 'description' => 'New connection form for E.ON Next gas supplies.'],
                ['file' => 'Lourades VAT form E.ON Next 1.pdf', 'title' => 'E.ON Next VAT Form', 'description' => 'VAT declaration form for E.ON Next.'],
                ['file' => 'Lourades Smartest LOA 1.pdf', 'title' => 'Smartest LOA', 'description' => 'Letter of Authority for Smartest.'],
                ['file' => 'POWWR Gas and Elec new connection form 1.docx', 'title' => 'POWWR Gas & Electricity New Connection', 'description' => 'New connection form for POWWR gas and electricity supplies.'],
                ['file' => 'TEM LOA 1.pdf', 'title' => 'TEM LOA', 'description' => 'Letter of Authority for TEM.'],
                ['file' => 'TEM DD Mandate Form 1.pdf', 'title' => 'TEM Direct Debit Mandate', 'description' => 'Direct Debit mandate form for TEM.'],
                ['file' => 'TEM VAT Form 1.pdf', 'title' => 'TEM VAT Form', 'description' => 'VAT declaration form for TEM.'],
                ['file' => 'TEM term and conditions 1.pdf', 'title' => 'TEM Terms & Conditions', 'description' => 'TEM supply terms and conditions.'],
                ['file' => 'UGP LOA 1.pdf', 'title' => 'UGP LOA', 'description' => 'Letter of Authority for UGP.'],
                ['file' => 'UGP Gas New Connection Form 1.pdf', 'title' => 'UGP Gas New Connection', 'description' => 'New connection form for UGP gas supplies.'],
                ['file' => 'UGP HH New Connection Form 1.pdf', 'title' => 'UGP Half-Hourly New Connection', 'description' => 'New connection form for UGP half-hourly electricity supplies.'],
            ],
        ],

        'ae' => [
            'title' => 'AE Knowledge Base',
            'description' => 'Guides and reference documents for Account Executives.',
            'folder' => 'agiledocs',
            'items' => [
                ['file' => 'AU Savers Call & Email Script.docx', 'title' => 'AU Savers Call & Email Script', 'description' => 'Call and email script for AU Savers customers.'],
                ['file' => 'Supplier Abbreveations. 2.docx', 'title' => 'Supplier Abbreviations', 'description' => 'Reference list of supplier abbreviations.'],
                ['file' => 'TEM LOA 1.pdf', 'title' => 'TEM LOA', 'description' => 'Letter of Authority for TEM.'],
                ['file' => 'TEM term and conditions 1.pdf', 'title' => 'TEM Terms & Conditions', 'description' => 'TEM supply terms and conditions.'],
                ['file' => 'POWWR Gas and Elec new connection form 1.docx', 'title' => 'POWWR Gas & Electricity New Connection', 'description' => 'New connection form for POWWR gas and electricity supplies.'],
            ],
        ],

    ],

];
