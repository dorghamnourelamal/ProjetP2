<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSalleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('salles')->insert([
            // France
            ['nom' => 'Zénith de Paris', 'capacite' => 6300, 'adresse' => '211 Avenue Jean Jaurès, 75019 Paris, France', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'L\'Olympia', 'capacite' => 2000, 'adresse' => '28 Boulevard des Capucines, 75009 Paris, France', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Palais des Congrès de Paris', 'capacite' => 3700, 'adresse' => '2 Place de la Porte Maillot, 75017 Paris, France', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Le Grand Rex', 'capacite' => 2650, 'adresse' => '1 Boulevard Poissonnière, 75002 Paris, France', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Le Transbordeur', 'capacite' => 1500, 'adresse' => '3 Boulevard Stalingrad, 69100 Villeurbanne, Lyon, France', 'created_at' => now(), 'updated_at' => now()],
            // Tunisie
            ['nom' => 'Théâtre de l\'Opéra de Tunis', 'capacite' => 1500, 'adresse' => 'Avenue Habib Bourguiba, 1000 Tunis, Tunisie', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Palais des Congrès de Tunis', 'capacite' => 3000, 'adresse' => 'Avenue de la Ligue des États Arabes, 1053 Tunis, Tunisie', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Théâtre Antique de Carthage', 'capacite' => 8000, 'adresse' => 'Site archéologique de Carthage, 2016 Carthage, Tunisie', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Salle de Fêtes El Mechtel', 'capacite' => 1200, 'adresse' => 'Avenue Ouled Haffouz, 1002 Tunis, Tunisie', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Palais des Congrès de Sousse', 'capacite' => 2500, 'adresse' => 'Boulevard du Maghreb, 4000 Sousse, Tunisie', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('events')->insert([
            // Événements France
            ['titre' => 'Nuit de la Guitare', 'description' => 'Festival de guitare acoustique et électrique avec des virtuoses internationaux. Une soirée unique au cœur de Paris.', 'date_event' => '2026-07-10', 'heure' => '20:00:00', 'heure_fin' => '23:30:00', 'places_disponibles' => 6300, 'prix' => 45.00, 'salle_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['titre' => 'Gala — Hommage à Jacques Brel', 'description' => 'Soirée hommage à Jacques Brel avec les plus grandes voix de la chanson française dans la mythique salle de l\'Olympia.', 'date_event' => '2026-07-28', 'heure' => '20:30:00', 'heure_fin' => '23:00:00', 'places_disponibles' => 2000, 'prix' => 65.00, 'salle_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['titre' => 'VivaTech 2026', 'description' => 'Le plus grand salon européen dédié à la technologie et à l\'innovation. Startups, grands groupes et investisseurs réunis pour 2 jours de conférences.', 'date_event' => '2026-09-17', 'heure' => '09:00:00', 'heure_fin' => '19:00:00', 'places_disponibles' => 3700, 'prix' => 120.00, 'salle_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['titre' => 'Festival du Film Fantastique', 'description' => 'Projections exclusives des meilleurs films de science-fiction et fantastique de l\'année, suivies de rencontres avec les réalisateurs.', 'date_event' => '2026-08-14', 'heure' => '19:00:00', 'heure_fin' => '23:59:00', 'places_disponibles' => 2650, 'prix' => 18.00, 'salle_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['titre' => 'Nuits Sonores Lyon', 'description' => 'Festival de musiques électroniques et cultures émergentes. DJs et live acts européens pour une nuit inoubliable à Lyon.', 'date_event' => '2026-09-05', 'heure' => '22:00:00', 'heure_fin' => '06:00:00', 'places_disponibles' => 1500, 'prix' => 28.00, 'salle_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            // Événements Tunisie
            ['titre' => 'Gala Lyrique de Tunis', 'description' => 'Soirée d\'opéra et de musique classique avec l\'Orchestre Symphonique Tunisien dans le cadre du Théâtre Municipal de l\'Opéra.', 'date_event' => '2026-07-18', 'heure' => '20:00:00', 'heure_fin' => '23:00:00', 'places_disponibles' => 1500, 'prix' => 30.00, 'salle_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['titre' => 'Forum Africain de l\'Innovation', 'description' => 'Conférence internationale réunissant entrepreneurs, investisseurs et décideurs africains autour du numérique et des nouvelles technologies.', 'date_event' => '2026-08-03', 'heure' => '09:00:00', 'heure_fin' => '18:00:00', 'places_disponibles' => 3000, 'prix' => 80.00, 'salle_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['titre' => 'Festival International de Carthage', 'description' => 'L\'un des plus grands festivals de la Méditerranée dans le cadre antique de Carthage : musique, danse, théâtre et spectacles sous les étoiles.', 'date_event' => '2026-07-25', 'heure' => '21:00:00', 'heure_fin' => '00:00:00', 'places_disponibles' => 8000, 'prix' => 50.00, 'salle_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['titre' => 'Soirée Malouf & Musique Andalouse', 'description' => 'Concert de malouf tunisien et musique andalouse, un voyage musical entre les deux rives de la Méditerranée.', 'date_event' => '2026-08-30', 'heure' => '20:30:00', 'heure_fin' => '23:00:00', 'places_disponibles' => 1200, 'prix' => 25.00, 'salle_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['titre' => 'Salon Mediterranean Business', 'description' => 'Rencontres B2B et tables rondes entre entrepreneurs tunisiens et européens. Networking, pitch de startups et ateliers pratiques.', 'date_event' => '2026-09-20', 'heure' => '09:00:00', 'heure_fin' => '17:00:00', 'places_disponibles' => 2500, 'prix' => 60.00, 'salle_id' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
