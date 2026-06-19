import { Component } from '@angular/core';
import { TrombinoscopeComponent } from './trombinoscope/trombinoscope';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [TrombinoscopeComponent],
  templateUrl: './app.html',
  styleUrl: './app.css'
})
export class App {}
