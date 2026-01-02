<?php

namespace app\Http\controllers;

use app\models\LandingService;
use app\models\LandingReview;
use app\models\LandingContact;
use app\models\LandingSetting;
use app\Http\Response;
use app\classes\UUID;
use app\classes\Validator;
use Illuminate\Http\Request;

class PublicController
{
    /**
     * Get all public settings for landing page
     */
    public function getSettings(Response $response)
    {
        $settings = LandingSetting::all()->pluck('value', 'key');
        return $response->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Get all active services for landing page
     */
    public function getServices(Response $response)
    {
        $services = LandingService::where('is_active', true)->get();
        return $response->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Get all reviews for landing page
     */
    public function getReviews(Response $response)
    {
        $reviews = LandingReview::orderBy('created_at', 'desc')->get();
        return $response->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Store a new contact message from landing page
     */
    public function storeContact(Request $request, Response $response, Validator $validator)
    {
        $data = json_decode($request->getContent(), true);

        $rules = [
            'name'    => 'required|min:2',
            'email'   => 'required|email',
            'message' => 'required|min:5'
        ];

        $validator->validate($data, $rules);

        $data['id'] = (string) UUID::v4();
        $data['created_at'] = date('Y-m-d H:i:s');

        $contact = LandingContact::create($data);

        return $response->success('Mensagem enviada com sucesso! Em breve entraremos em contato.', $contact, 201);
    }
}
