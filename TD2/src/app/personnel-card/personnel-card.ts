import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-personnel-card',
  imports: [],
  templateUrl: './personnel-card.html',
  styleUrl: './personnel-card.css',
})
export class PersonnelCard {
  @Input() personnel: any;
}
