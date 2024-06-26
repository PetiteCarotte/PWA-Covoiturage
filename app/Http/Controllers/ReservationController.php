<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Trajet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function ReserverTrajet(Request $request)
        {
            
            $id = Auth::user()->Id_Utilisateur;

            $DateNow = now();
            Log::info('Date actuelle : ' . $DateNow);
            $ValidatedData = $request->validate([
                'Id_Trajet' => 'required|integer|exists:Trajet,Id_Trajet',
                'Id_Adresse' => 'required|integer|exists:Adresse,Id_Adresse',
            ]);
            $ValidatedData["Id_Passager"] = $id;
            $ValidatedData['Date_Reservation'] = $DateNow;
            $ValidatedData['Statut'] = 0;

            $trajet = Trajet::find($ValidatedData['Id_Trajet']);

            // Le trajet n'a pas été trouvé => erreur
            if (!$trajet) {
                return redirect()->back()->withErrors(['Id_Trajet' => 'Trajet introuvable.']);
            }

            //Verifier si l'utilisateur a deja reservé le trajet 
            if (Reservation::where('Id_Passager', $id)->where('Id_Trajet', $ValidatedData['Id_Trajet'])->exists()) {
                //Afficher le message d'erreur dans la console 
                Log::info('Vous avez déjà réservé ce trajet.');
                
                return response()->json(['error' => 'Vous avez déjà réservé ce trajet.'], 404);
            }

            $nbPassagers = $trajet->reservations()
                ->where('Statut', '!=', 2)
                ->count();

            if ($nbPassagers + 1 >= $trajet->Nbre_Places) {
                if ($nbPassagers + 1 > $trajet->Nbre_Places) {
                    return redirect()->back()->withErrors(['places' => 'Le nombre de places disponibles a déjà été atteint.']);
                }

                $trajet->Statut = false; // Si le trajet est complet, il n'est plus visible
                $trajet->save();
            }

            $Reservation = Reservation::create($ValidatedData);

            // TODO : Ajouter domicile de l'utilisateur est base du trajet

            return redirect('/accueil')->with('success', 'Réservation créé avec succès!');
        }

    public function getReservation($id)
    {
        try {
            $reservation = Reservation::find($id);

            return [
                'nomPassager' => $reservation->utilisateur->Nom,
                'prenomPassager' => $reservation->utilisateur->Prenom,
                'unite' => $reservation->utilisateur->Unite,
                'domicile' => $reservation->adresse ? $reservation->adresse->Intitule : null,
            ];
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function accepterReservation($id)
    {
        try {
            $reservation = Reservation::find($id);

            if (!$reservation) {
                return response()->json(['message' => 'Reservation not found'], 404);
            }

            $reservation->Statut = 1;
            $reservation->save();

            return response()->json(['message' => 'Reservation accepted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function refuserReservation($id)
    {
        try {
            $reservation = Reservation::find($id);

            if (!$reservation) {
                return response()->json(['message' => 'Reservation not found'], 404);
            }

            $reservation->Statut = 2;

            $trajet = Trajet::find($reservation->Id_Trajet);
            $trajet->Statut = true; // Si le trajet était complet, il redevient visible

            $trajet->save();

            $reservation->save();

            return response()->json(['message' => 'Reservation accepted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function annulerReservation(Request $request)
    {
        $userId = Auth::user()->Id_Utilisateur;
        $validatedData = $request->validate([
            'Id_Trajet' => 'required|integer|exists:Trajet,Id_Trajet',
        ]);

        $reservation = Reservation::where('Id_Trajet', $validatedData['Id_Trajet'])
                                  ->where('Id_Passager', $userId)
                                  ->first();

        if (!$reservation) {
            return response()->json(['error' => 'Réservation introuvable'], 404);
        }

        $reservation->delete();

        // Mettre à jour le statut du trajet si nécessaire
        $trajet = Trajet::find($validatedData['Id_Trajet']);
        $nbPassagers = $trajet->reservations()->where('Statut', '!=', 2)->count();
        if ($nbPassagers < $trajet->Nbre_Places) {
            $trajet->Statut = true;
            $trajet->save();
        }

        return response()->json(['success' => 'Réservation annulée avec succès'], 200);
    }
    
}
