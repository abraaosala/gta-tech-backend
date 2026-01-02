<?php

namespace app\Http\controllers\admin;

use app\models\LandingService;
use app\models\LandingSetting;
use app\models\LandingContact;
use app\Http\Response;
use app\classes\UUID;
use app\classes\Validator;
use Illuminate\Http\Request;

class LandingController
{
    /**
     * List all settings for admin
     */
    public function getSettings(Response $response)
    {
        $settings = LandingSetting::all();
        return $response->json($settings);
    }

    /**
     * Update a specific setting
     */
    public function updateSetting(Request $request, Response $response, Validator $validator)
    {
        $data = json_decode($request->getContent(), true);

        $rules = [
            'key' => 'required',
            'value' => 'required'
        ];

        $validator->validate($data, $rules);

        $setting = LandingSetting::find($data['key']);
        if (!$setting) {
            return $response->error('Configuração não encontrada', [], 404);
        }

        $setting->update(['value' => $data['value']]);

        return $response->success('Configuração atualizada com sucesso', $setting);
    }

    /**
     * List all contacts/leads
     */
    public function getContacts(Response $response)
    {
        $contacts = LandingContact::orderBy('created_at', 'desc')->get();
        return $response->json($contacts);
    }

    /**
     * Delete a contact
     */
    public function deleteContact($id, Response $response)
    {
        $contact = LandingContact::find($id);
        if (!$contact) {
            return $response->error('Contato não encontrado', [], 404);
        }

        $contact->delete();
        return $response->success('Contato excluído com sucesso');
    }
}
