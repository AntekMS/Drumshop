<?php

namespace App\Controllers;

class StaticPages extends BaseController
{
    /**
     * Zeigt die AGB-Seite an
     */
    public function agb()
    {
        $data = [
            'title' => 'Allgemeine Geschäftsbedingungen'
        ];

        return view('templates/header', $data)
            . view('static/agb', $data)
            . view('templates/footer');
    }

    /**
     * Zeigt die Impressum-Seite an
     */
    public function impressum()
    {
        $data = [
            'title' => 'Impressum'
        ];

        return view('templates/header', $data)
            . view('static/impressum', $data)
            . view('templates/footer');
    }

    /**
     * Zeigt die Über uns-Seite an
     */
    public function ueber_uns()
    {
        $data = [
            'title' => 'Über uns'
        ];

        return view('templates/header', $data)
            . view('static/ueber_uns', $data)
            . view('templates/footer');
    }

    /**
     * Zeigt die Datenschutz-Seite an
     */
    public function datenschutz()
    {
        $data = [
            'title' => 'Datenschutzerklärung'
        ];

        return view('templates/header', $data)
            . view('static/datenschutz', $data)
            . view('templates/footer');
    }
}