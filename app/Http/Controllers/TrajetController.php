<?php

namespace App\Http\Controllers;

use App\Models\Trajet;
use App\Models\Adresse;
use App\Models\Jours;
use App\Models\Utilisateur;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TrajetController extends Controller
{
    public function getAllTrajets($date)
    {
        try {
            // Récupérer tous les trajets avec les relations nécessaires
            $trajets = Trajet::with(['domicile', 'base', 'utilisateur'])
                ->where('Statut', '=', true) // On exclut les trajets complets
                ->whereDate("Date_Depart", "=", $date)
                ->get();

            $result = $trajets->map(function ($trajet) {

                $domicile = $trajet->domicile;
                $base = $trajet->base;

                $ptDepart = null;
                $ptArrive = null;
                $heure = null;

                if ($domicile && $base) {
                    if ($trajet->Domicile_Base) { //Trajet de la base jusqu'au domicile 
                        $ptDepart = $base->Intitule;
                        $ptArrive = $domicile->Intitule;
                        $heure = "Départ de la base à  " . date('H\hi', strtotime($trajet->Heure_Depart));
                       
                    } else { //Ou départ du domicile à 
                        $ptDepart = $domicile->Intitule;
                        $ptArrive = $base->Intitule;
                        $heure = "Départ du domicile à " . date('H\hi', strtotime($trajet->Heure_Depart));
                    }
                }

                $type = $trajet->Trajet_Regulier ? "Régulier" : "Ponctuel";

                if ($trajet->Trajet_Regulier) {
                    $dateString = "Trajet Régulier"; // TODO : Gérer formattage de la date pour les trajets réguliers (prochain trajet lundi par exemple)
                } else {
                    $dateDepart = new \DateTime($trajet->Date_Depart);
                    $jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
                    $mois = ["", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
                    $dateString = $jours[$dateDepart->format('w')] . " " . $dateDepart->format('j') . " " . $mois[$dateDepart->format('n')];
                }

                $utilisateur = $trajet->utilisateur;

                return [
                    'idTrajet' => $trajet->Id_Trajet,
                    'ptDepart' => $ptDepart,
                    'ptArrive' => $ptArrive,
                    'typeTrajet' => $type,
                    'heureDepart' => $heure,
                    'date' => $dateString,
                    'prenomConducteur' => $utilisateur ? $utilisateur->Prenom : null,
                    'nomConducteur' => $utilisateur ? $utilisateur->Nom : null,
                    'uniteConducteur' => $utilisateur ? $utilisateur->Unite : null,
                ];
            });

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }


    public function getAllTrajetsReguliers()
    {
        try {
            // Recuperer tous les trajets avec les relations nécessaires
            $trajets = Trajet::with(['domicile', 'base', 'utilisateur'])
                ->where('Statut', '=', true) // On exclut les trajets complets
                ->where('Trajet_Regulier', '=', true)
                ->get();


            $result = $trajets->map(function ($trajet) {

                $domicile = $trajet->domicile;
                $base = $trajet->base;

                $ptDepart = null;
                $ptArrive = null;

                if ($domicile && $base) {
                    if ($trajet->Domicile_Base) { //Trajet de la base jusqu'au domicile 
                        $ptDepart = $base->Intitule;
                        $ptArrive = $domicile->Intitule;
                        $heure =date('H\hi', strtotime($trajet->Heure_Depart));
                       
                    } else { //Ou départ du domicile à 
                        $ptDepart = $domicile->Intitule;
                        $ptArrive = $base->Intitule;
                        $heure =date('H\hi', strtotime($trajet->Heure_Depart));
                    }
                }

                $type = $trajet->Trajet_Regulier ? "Régulier" : "Ponctuel";

                $utilisateur = $trajet->utilisateur;

                return [
                    'idTrajet' => $trajet->Id_Trajet,
                    'ptDepart' => $ptDepart,
                    'ptArrive' => $ptArrive,
                    'typeTrajet' => $type,
                    'heureDepart' => $heure,
                    'Date_Depart' => $trajet->Date_Depart,
                    'prenomConducteur' => $utilisateur ? $utilisateur->Prenom : null,
                    'nomConducteur' => $utilisateur ? $utilisateur->Nom : null,
                    'uniteConducteur' => $utilisateur ? $utilisateur->Unite : null,
                ];
            });

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function getTrajet($id)
    {
        try {
            // Retrieve the trajet
            $trajet = Trajet::with(['domicile', 'base', 'utilisateur', 'reservations.utilisateur'])->find($id);

            if ($trajet) {
                // Retrieve the domicile and base addresses
                $domicile = $trajet->domicile;
                $base = $trajet->base;

                $ptDepart = null;
                $ptArrive = null;
                $heureDepart = null;
                $heureArrive = null;

                if ($domicile && $base) {
                    if ($trajet->Domicile_Base) { //Trajet de la base jusqu'au domicile 
                        $ptDepart = $base->Intitule;
                        $ptArrive = $domicile->Intitule;
                        $heure = "Départ de la base à  " . date('H\hi', strtotime($trajet->Heure_Depart));
                       
                    } else { //Ou départ du domicile à 
                        $ptDepart = $domicile->Intitule;
                        $ptArrive = $base->Intitule;
                        $heure = "Départ du domicile à " . date('H\hi', strtotime($trajet->Heure_Depart));
                    }
                }

                $type = $trajet->Trajet_Regulier ? "Régulier" : "Ponctuel";

                // Information conducteur
                $utilisateur = $trajet->utilisateur;

                // information passengers
                $passagers = $trajet->reservations()
                    ->where('Statut', '!=', 2)->with(['utilisateur', 'adresse'])->get()->map(function ($reservation) {
                        return [
                            'nomPassager' => $reservation->utilisateur->Nom,
                            'prenomPassager' => $reservation->utilisateur->Prenom,
                            'telephone' => $reservation->utilisateur->Numero_De_Telephone,
                            'statut' => $reservation->Statut,
                            'unite' => $reservation->utilisateur->Unite,
                            'adresse' => $reservation->adresse,
                            'idReservation' => $reservation->Id_Reservation,
                        ];
                    });

                $nbPassagers = $passagers->count();

                $result = [
                    'idTrajet' => $trajet->Id_Trajet,
                    'ptDepart' => $ptDepart,
                    'ptArrive' => $ptArrive,
                    'typeTrajet' => $type,
                    'heure' => $heure,
                    'Date_Depart' => $trajet->Date_Depart,
                    'nomConducteur' => $utilisateur ? $utilisateur->Nom : null,
                    'prenomConducteur' => $utilisateur ? $utilisateur->Prenom : null,
                    'uniteConducteur' => $utilisateur ? $utilisateur->Unite : null,
                    'passagers' => $passagers,
                    'nbPassagers' => $nbPassagers,
                    'nbMaxPassagers' => $trajet->Nbre_Places
                ];

                return response()->json($result, 200);
            } else {
                return response()->json(['message' => 'Trajet non trouvé'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function getAllTrajetsPassagers()
    {
        try {
            $id = Auth::user()->Id_Utilisateur;

            if (!is_numeric($id)) {
                return response()->json(['message' => 'ID utilisateur invalide'], 400);
            }

            $reservations = Reservation::with(['trajet.domicile', 'trajet.base', 'trajet.utilisateur'])
                ->where('Id_Passager', $id)
                ->where('Statut', '!=', 2)
                ->get();

            if ($reservations->isEmpty()) {
                return response()->json(['message' => 'Aucun trajet trouvé pour ce passager'], 404);
            }

            $result = $reservations->map(function ($reservation) {
                $trajet = $reservation->trajet;
                if (!$trajet) {
                    return [
                        'idTrajet' => null,
                        'ptDepart' => null,
                        'ptArrive' => null,
                        'typeTrajet' => null,
                        'heureDepart' => null,
                        'heureArrive' => null,
                        'Date_Depart' => null,
                        'nomConducteur' => null,
                        'uniteConducteur' => null,
                        'nbMaxPassagers' => null
                    ];
                }

                $domicile = $trajet->domicile;
                $base = $trajet->base;

                $ptDepart = null;
                $ptArrive = null;
                $heure = null;

                if ($domicile && $base) {
                    if ($trajet->Domicile_Base) { //Trajet de la base jusqu'au domicile 
                        $ptDepart = $base->Intitule;
                        $ptArrive = $domicile->Intitule;
                        $heure = "Départ de la base à  " . date('H\hi', strtotime($trajet->Heure_Depart));
                       
                    } else { //Ou départ du domicile à 
                        $ptDepart = $domicile->Intitule;
                        $ptArrive = $base->Intitule;
                        $heure = "Départ du domicile à " . date('H\hi', strtotime($trajet->Heure_Depart));
                    }
                }

                $type = $trajet->Trajet_Regulier ? "Régulier" : "Ponctuel";

                if ($trajet->Trajet_Regulier) {
                    $dateString = "Trajet Régulier"; // TODO : Gérer formattage de la date pour les trajets régulier (prochain trajet lundi par exemple)
                } else {
                    $dateDepart = new \DateTime($trajet->Date_Depart);
                    $jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
                    $mois = ["", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
                    $dateString = $jours[$dateDepart->format('w')] . " " . $dateDepart->format('j') . " " . $mois[$dateDepart->format('n')];
                }

                return [
                    'idTrajet' => $trajet->Id_Trajet,
                    'date' => $dateString,
                    'ptDepart' => $ptDepart,
                    'ptArrive' => $ptArrive,
                    'heure' => $heure,
                    'prenomConducteur' => $trajet->utilisateur ? $trajet->utilisateur->Prenom : null,
                    'nomConducteur' => $trajet->utilisateur ? $trajet->utilisateur->Nom : null,
                ];
            });

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function getAllTrajetsConducteurs()
    {
        try {
            $id = Auth::user()->Id_Utilisateur;

            if (!is_numeric($id)) {
                return response()->json(['message' => 'ID utilisateur invalide'], 400);
            }

            $trajets = Trajet::with(['domicile', 'base', 'utilisateur'])
                ->where('Id_Conducteur', $id)
                ->get();

            if ($trajets->isEmpty()) {
                return response()->json(['message' => 'Aucun trajet trouvé pour ce conducteur'], 404);
            }

            $result = $trajets->map(function ($trajet) {
                $domicile = $trajet->domicile;
                $base = $trajet->base;

                $ptDepart = null;
                $ptArrive = null;
                $heure = null;

                if ($domicile && $base) {
                    if ($trajet->Domicile_Base) { //Trajet de la base jusqu'au domicile 
                        $ptDepart = $base->Intitule;
                        $ptArrive = $domicile->Intitule;
                        $heure = "Départ de la base à  " . date('H\hi', strtotime($trajet->Heure_Depart));
                       
                    } else { //Ou départ du domicile à 
                        $ptDepart = $domicile->Intitule;
                        $ptArrive = $base->Intitule;
                        $heure = "Départ du domicile à " . date('H\hi', strtotime($trajet->Heure_Depart));
                    }
                }

                $nouvellesDemandes = False;
                if ($trajet->reservations->contains('Statut', 0)) {
                    $nouvellesDemandes = True;
                }

                $passagers = $trajet->reservations->where('Statut', '!=', 2)->map(function ($reservation) {
                    return [
                        'nomPassager' => $reservation->utilisateur->Nom,
                        'prenomPassager' => $reservation->utilisateur->Prenom,
                        'adresse' => $reservation->adresse ? $reservation->adresse->Intitule : null,
                    ];
                });

                $nbPassagers = $passagers->count();
                if ($trajet->Nbre_Places > $nbPassagers) {
                    $status = "En ligne";
                } else {
                    $status = "Complet";
                }

                $type = $trajet->Trajet_Regulier ? "Régulier" : "Ponctuel";

                if ($trajet->Trajet_Regulier) {
                    $dateString = "Trajet Régulier"; // TODO : Gérer formattage de la date pour les trajets régulier (prochain trajet lundi par exemple)
                } else {
                    $dateDepart = new \DateTime($trajet->Date_Depart);
                    $jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
                    $mois = ["", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
                    $dateString = $jours[$dateDepart->format('w')] . " " . $dateDepart->format('j') . " " . $mois[$dateDepart->format('n')];
                }


                return [
                    'idTrajet' => $trajet->Id_Trajet,
                    'date' => $dateString,
                    'ptDepart' => $ptDepart,
                    'ptArrive' => $ptArrive,
                    'heure' => $heure,
                    'prenomConducteur' => $trajet->utilisateur ? $trajet->utilisateur->Prenom : null,
                    'nomConducteur' => $trajet->utilisateur ? $trajet->utilisateur->Nom : null,
                    'nbMaxPassagers' => $trajet->Nbre_Places,
                    'nbPassagers' => $nbPassagers,
                    'status' => $status,
                    'nouvellesDemandes' => $nouvellesDemandes,
                ];
            });

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }


    // public function getAllPropositionsTrajets() {
    //     // TODO : récupère tout les trajets que l'utilisateur (utiliser l'id de Auth) a proposé
    // }



    public function updateTrajet(Request $request, $id)
    {
        try {
            // Assurer que les données sont valides
            $validatedData = $request->validate([
                'Date_Depart' => 'required|date',
                'Heure_Depart' => 'required|date_format:H:i',
                'Nbre_Places' => 'required|integer',
                'Qte_Bagages' => 'required|integer',
                'Description' => 'nullable|string|max:300',
                'Trajet_Regulier' => 'required|boolean',
                'Statut' => 'required|boolean',
                'Domicile_Base' => 'required|boolean',
                'Id_Domicile' => 'required|exists:Adresse,Id_Adresse',
                'Id_Base' => 'required|exists:Adresse,Id_Adresse',
                'Id_Jours' => 'nullable|array|size:7',
                'Id_Jours.*' => 'required|boolean',
                'Id_Conducteur' => 'required|exists:Utilisateur,Id_Utilisateur',
            ]);


            $trajet = Trajet::find($id);

            // Dans le cas où le trajet n'est pas trouvé
            if (!$trajet) {
                return response()->json(['message' => 'Trajet not found'], 404);
            }

            // Recuperer l'ID du jour associé au trajet
            $idJour = $trajet->Id_Jours;


            $jours = Jours::find($idJour);

            // Mise a jour du jour
            if ($jours && !is_null($validatedData['Id_Jours'])) {
                $jours->update([
                    'Lundi' => $validatedData['Id_Jours'][0],
                    'Mardi' => $validatedData['Id_Jours'][1],
                    'Mercredi' => $validatedData['Id_Jours'][2],
                    'Jeudi' => $validatedData['Id_Jours'][3],
                    'Vendredi' => $validatedData['Id_Jours'][4],
                    'Samedi' => $validatedData['Id_Jours'][5],
                    'Dimanche' => $validatedData['Id_Jours'][6],
                ]);


                $validatedData['Id_Jours'] = $jours->Id_Jours;
            }


            $trajet->update($validatedData);


            return response()->json(['message' => 'Trajet updated successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function createTrajet(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'Date_Depart' => 'required|date',
                'Heure_Depart' => 'required|date_format:H:i',
                'Nbre_Places' => 'required|integer|min:0',
                'Qte_Bagages' => 'required|integer|min:0',
                'Description' => 'nullable|string|max:255',
                'Trajet_Regulier' => 'required|boolean',
                'Domicile_Base' => 'required|boolean',
                'ptDepart' => 'required|string|max:255',
                'ptArrive' => 'required|string|max:255',
                'jours' => 'required|array|size:7',
                'jours.*' => 'required|boolean',
            ]);

            $conducteur = Auth::user();

            $adresseDepart = Adresse::where('Intitule', $validatedData['ptDepart'])->first();
            $adresseArrive = Adresse::where('Intitule', $validatedData['ptArrive'])->first();
            if ($validatedData['Domicile_Base']) {
                $base = $adresseDepart;
                $domicile = $adresseArrive;
            } else {
                $base = $adresseArrive;
                $domicile = $adresseDepart;
            }

            if (!$adresseDepart || !$adresseArrive) {
                return response()->json(['message' => 'Address not found'], 404);
            }

            $jours = Jours::firstOrCreate([
                'Lundi' => $validatedData['jours'][0],
                'Mardi' => $validatedData['jours'][1],
                'Mercredi' => $validatedData['jours'][2],
                'Jeudi' => $validatedData['jours'][3],
                'Vendredi' => $validatedData['jours'][4],
                'Samedi' => $validatedData['jours'][5],
                'Dimanche' => $validatedData['jours'][6],
            ]);

            $trajet = new Trajet([
                'Date_Depart' => $validatedData['Date_Depart'],
                'Heure_Depart' => $validatedData['Heure_Depart'],
                'Qte_Bagages' => $validatedData['Qte_Bagages'],
                'Description' => $validatedData['Description'],
                'Trajet_Regulier' => $validatedData['Trajet_Regulier'],
                'Statut' => true,
                'Nbre_Places' => $validatedData['Nbre_Places'],
                'Domicile_Base' => $validatedData['Domicile_Base'],
                'Id_Domicile' => $domicile->Id_Adresse,
                'Id_Base' => $base->Id_Adresse,
                'Id_Jours' => $jours->Id_Jours,
                'Id_Conducteur' => $conducteur->Id_Utilisateur,
            ]);

            $trajet->save();

            return response()->json(['message' => 'Trajet created successfully', 'trajet' => $trajet], 201);
        } catch (\Exception $e) {

            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function supprimerTrajet($id)
    {
        $userId = Auth::user()->Id_Utilisateur;

        $trajet = Trajet::where('Id_Trajet', $id)
            ->where('Id_Conducteur', $userId)
            ->first();

        if (!$trajet) {
            return response()->json(['error' => 'Trajet introuvable ou non autorisé'], 404);
        }

        // Vérifier s'il y a des réservations
        $reservations = Reservation::where('Id_Trajet', $id)->get();
        if ($reservations->isNotEmpty()) {
            // Supprimer les réservations associées
            foreach ($reservations as $reservation) {
                $reservation->delete();
            }
        }

        $trajet->delete();

        return response()->json(['success' => 'Trajet supprimé avec succès'], 200);
    }

}
