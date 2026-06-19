import { Component , Input} from '@angular/core';

@Component({
  selector: 'app-etudiant-card',
  imports: [],
  templateUrl: './etudiant-card.html',
  styleUrl: './etudiant-card.css',
})
export class EtudiantCard {
  @Input() etudiant: any;
}
