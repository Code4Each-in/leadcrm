<?php

namespace App\Http\Controllers;

class KnowledgeBaseController extends Controller
{
    /**
     * Static for now - the client will supply real content later. Keeping
     * the data assembly here (rather than inline in the Blade view) means
     * swapping this for a database-backed lookup later only touches this
     * method's body; the view stays untouched.
     */
    public function index()
    {
        $categories = [
            'document' => ['label' => 'Documents', 'icon' => 'mdi-file-document-outline'],
            'link'     => ['label' => 'Links', 'icon' => 'mdi-open-in-new'],
            'video'    => ['label' => 'Videos', 'icon' => 'mdi-play-circle-outline'],
            'policy'   => ['label' => 'Policies', 'icon' => 'mdi-shield-check-outline'],
            'faq'      => ['label' => 'FAQs', 'icon' => 'mdi-help-circle-outline'],
        ];

        $items = [
            [
                'type'        => 'document',
                'title'       => 'Employee Handbook',
                'description' => 'Company policies, working hours, and conduct guidelines.',
                'meta'        => 'PDF',
                'url'         => '#',
            ],
            [
                'type'        => 'document',
                'title'       => 'Lead Handling SOP',
                'description' => 'Step-by-step process for qualifying and assigning leads.',
                'meta'        => 'PDF',
                'url'         => '#',
            ],
            [
                'type'        => 'link',
                'title'       => 'CRM Support Portal',
                'description' => 'Raise a ticket or browse existing support articles.',
                'meta'        => 'External',
                'url'         => '#',
            ],
            [
                'type'        => 'link',
                'title'       => 'Companies House Lookup',
                'description' => 'Verify company registration details.',
                'meta'        => 'External',
                'url'         => '#',
            ],
            [
                'type'        => 'video',
                'title'       => 'Getting Started with AGILE ONE',
                'description' => 'A 5-minute walkthrough of the dashboard and lead pipeline.',
                'meta'        => '5:12',
                'url'         => '#',
            ],
            [
                'type'        => 'video',
                'title'       => 'Attendance & Break Tracking',
                'description' => 'How to punch in, take breaks, and view your hours.',
                'meta'        => '3:40',
                'url'         => '#',
            ],
            [
                'type'        => 'policy',
                'title'       => 'Data Protection & Privacy Policy',
                'description' => 'How customer and employee data is collected and handled.',
                'meta'        => 'Policy',
                'url'         => '#',
            ],
            [
                'type'        => 'policy',
                'title'       => 'Terms & Conditions',
                'description' => 'Terms of use for internal systems and tools.',
                'meta'        => 'Terms',
                'url'         => '#',
            ],
            [
                'type'        => 'faq',
                'title'       => 'How do I reset my OTP login?',
                'description' => 'Contact your administrator to disable OTP temporarily, or wait for the lock period to expire.',
                'meta'        => 'FAQ',
                'url'         => '#',
            ],
            [
                'type'        => 'faq',
                'title'       => 'Who can see my attendance records?',
                'description' => 'You and any Admin/Super Admin on your account.',
                'meta'        => 'FAQ',
                'url'         => '#',
            ],
        ];

        return view('knowledge-base.index', compact('categories', 'items'));
    }
}
