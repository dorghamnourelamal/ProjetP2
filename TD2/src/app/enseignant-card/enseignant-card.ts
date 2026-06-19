import { Component,Input } from '@angular/core';

@Component({
  selector: 'app-enseignant-card',
  imports: [],
  templateUrl: './enseignant-card.html',
  styleUrl: './enseignant-card.css',
})
export class EnseignantCard {
  @Input() enseignant: any;
}
