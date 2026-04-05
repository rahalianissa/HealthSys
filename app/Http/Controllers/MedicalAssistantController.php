<?php

namespace App\Http\Controllers;

use App\Models\SymptomAnalysis;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MedicalAssistantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('patient.assistant');
    }

    public function analyze(Request $request)
    {
        try {
            $request->validate([
                'symptoms' => 'required|string|min:3',
            ]);

            $symptoms = strtolower($request->symptoms);
            
            // Mapping symptômes -> spécialités
            $specialty = $this->getSpecialty($symptoms);
            $urgency = $this->getUrgency($symptoms);
            $advice = $this->getAdvice($specialty, $urgency);
            $recommendations = $this->getRecommendations($urgency);
            
            // Sauvegarder l'analyse
            if (auth()->user()->patient) {
                SymptomAnalysis::create([
                    'patient_id' => auth()->user()->patient->id,
                    'symptoms' => $symptoms,
                    'suggested_specialty' => $specialty,
                    'urgency_level' => $urgency['level'],
                    'advice' => $advice,
                ]);
            }
            
            // Trouver un médecin
            $doctor = Doctor::with('user')->where('specialty', $specialty)->first();
            
            return response()->json([
                'success' => true,
                'specialty' => $specialty,
                'urgency' => $urgency,
                'advice' => $advice,
                'recommendations' => $recommendations,
                'doctor' => $doctor ? [
                    'id' => $doctor->id,
                    'name' => $doctor->user->name
                ] : null,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Assistant error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ], 500);
        }
    }
    
    private function getSpecialty($symptoms)
    {
        $map = [
            'cardiaque' => ['specialty' => 'Cardiologue', 'keywords' => ['poitrine', 'cœur', 'palpitation', 'cardiaque', 'coeur']],
            'respiratoire' => ['specialty' => 'Pneumologue', 'keywords' => ['toux', 'souffle', 'respiration', 'asthme', 'poumon']],
            'digestif' => ['specialty' => 'Gastro-entérologue', 'keywords' => ['estomac', 'ventre', 'nausée', 'vomissement', 'digestion']],
            'neurologique' => ['specialty' => 'Neurologue', 'keywords' => ['tête', 'migraine', 'vertige', 'nerf', 'cerveau']],
            'dermatologique' => ['specialty' => 'Dermatologue', 'keywords' => ['peau', 'rougeur', 'démangeaison', 'éruption', 'bouton']],
            'orthopedique' => ['specialty' => 'Orthopédiste', 'keywords' => ['os', 'articulation', 'dos', 'genou', 'épaule']],
        ];
        
        foreach ($map as $data) {
            foreach ($data['keywords'] as $keyword) {
                if (strpos($symptoms, $keyword) !== false) {
                    return $data['specialty'];
                }
            }
        }
        
        return 'Médecin généraliste';
    }
    
    private function getUrgency($symptoms)
    {
        $emergency = ['urgence', 'grave', 'saignement', 'étouffement', 'perte connaissance', 'crise cardiaque'];
        foreach ($emergency as $keyword) {
            if (strpos($symptoms, $keyword) !== false) {
                return ['level' => 'emergency', 'message' => 'URGENT - Consultez immédiatement !'];
            }
        }
        
        $high = ['forte fièvre', 'douleur intense', 'vomissement', 'déshydratation'];
        foreach ($high as $keyword) {
            if (strpos($symptoms, $keyword) !== false) {
                return ['level' => 'high', 'message' => 'Priorité élevée - Consultez dans les 24h'];
            }
        }
        
        if (strpos($symptoms, 'fièvre') !== false || strpos($symptoms, 'douleur') !== false) {
            return ['level' => 'medium', 'message' => 'Priorité moyenne - Consultez sous 48h'];
        }
        
        return ['level' => 'low', 'message' => 'Consultation normale - Prenez rendez-vous cette semaine'];
    }
    
    private function getAdvice($specialty, $urgency)
    {
        $advice = "🔍 **Analyse de vos symptômes :**\n\n";
        $advice .= "**Spécialité recommandée :** {$specialty}\n";
        $advice .= "**Niveau d'urgence :** {$urgency['message']}\n\n";
        $advice .= "**💡 Conseils :**\n";
        
        if ($urgency['level'] == 'emergency') {
            $advice .= "⚠️ Appelez immédiatement le 15 ou rendez-vous aux urgences !";
        } elseif ($urgency['level'] == 'high') {
            $advice .= "• Consultez un médecin dans les 24 heures\n";
            $advice .= "• Reposez-vous et évitez les efforts\n";
            $advice .= "• Buvez beaucoup d'eau";
        } elseif ($urgency['level'] == 'medium') {
            $advice .= "• Prenez rendez-vous dans les 48 heures\n";
            $advice .= "• Surveillez l'évolution de vos symptômes\n";
            $advice .= "• Reposez-vous suffisamment";
        } else {
            $advice .= "• Prenez rendez-vous cette semaine\n";
            $advice .= "• Maintenez une bonne hygiène de vie\n";
            $advice .= "• Notez l'évolution de vos symptômes";
        }
        
        return $advice;
    }
    
    private function getRecommendations($urgency)
    {
        if ($urgency['level'] == 'emergency') {
            return ["Appelez le 15 immédiatement", "Ne prenez pas de médicaments", "Ne conduisez pas"];
        } elseif ($urgency['level'] == 'high') {
            return ["Consultez rapidement", "Reposez-vous", "Buvez de l'eau"];
        } else {
            return ["Prenez rendez-vous", "Reposez-vous", "Évitez l'automédication"];
        }
    }
}