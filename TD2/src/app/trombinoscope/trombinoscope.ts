import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

import { EtudiantCardComponent } from '../etudiant-card/etudiant-card';
import { EnseignantCardComponent } from '../enseignant-card/enseignant-card';
import { PersonnelCardComponent } from '../personnel-card/personnel-card';
interface Etudiant {
  nom: string;
  prenom: string;
  email: string;
  telephone: string;
  photo: string;
  linkedin: string;
  alumni: string;
}

interface Enseignant {
  nom: string;
  prenom: string;
  email: string;
  telephone: string;
  photo: string;
  bureau: string;
  laboratoire: string;
  researchGate: string;
}

interface Personnel {
  nom: string;
  prenom: string;
  email: string;
  telephone: string;
  photo: string;
  bureau: string;
  service: string;
  twitter: string;
}




@Component({
  selector: 'app-trombinoscope',
  standalone: true,
  imports: [
    CommonModule,
    EtudiantCardComponent,
    EnseignantCardComponent,
    PersonnelCardComponent
  ],
  templateUrl: './trombinoscope.html',
  styleUrl: './trombinoscope.css'
})
export class TrombinoscopeComponent {


  etudiants: Etudiant[] = [
    {
      nom: 'Dorgham',
      prenom: 'Nour',
      email: 'nour.dorgham@utbm.fr',
      telephone: '06 11 22 33 44',
      photo: 'assets/user.png',
      linkedin: 'https://linkedin.com',
      alumni: 'https://alumni.utbm.fr'
    },
    {
      nom: 'Martin',
      prenom: 'Alice',
      email: 'alice.martin@utbm.fr',
      telephone: '06 55 44 33 22',
      photo: 'assets/user.png',
      linkedin: 'https://linkedin.com',
      alumni: 'https://alumni.utbm.fr'
    }
  ];

  enseignants: Enseignant[] = [
    {
      nom: 'Kas',
      prenom: 'Mohamed',
      email: 'mohamed.kas@utbm.fr',
      telephone: '03 84 00 00 00',
      photo: 'assets/user.png',
      bureau: 'B203',
      laboratoire: 'CIAD',
      researchGate: 'https://researchgate.net'
    },
    {
      nom: 'Durand',
      prenom: 'Sophie',
      email: 'sophie.durand@utbm.fr',
      telephone: '03 84 11 22 33',
      photo: 'assets/user.png',
      bureau: 'A105',
      laboratoire: 'RECITS',
      researchGate: 'https://researchgate.net'
    }
  ];

  personnels: Personnel[] = [
    {
      nom: 'Bernard',
      prenom: 'Paul',
      email: 'paul.bernard@utbm.fr',
      telephone: '03 84 99 88 77',
      photo: 'assets/user.png',
      bureau: 'C101',
      service: 'Scolarité',
      twitter: 'https://x.com'
    },
    {
      nom: 'Petit',
      prenom: 'Emma',
      email: 'emma.petit@utbm.fr',
      telephone: '03 84 66 55 44',
      photo: 'assets/user.png',
      bureau: 'D202',
      service: 'Administration',
      twitter: 'https://x.com'
    }
  ];
}
