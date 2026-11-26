import { Component } from '@angular/core';
import { Clientes } from './clientes/clientes';

@Component({
  selector: 'app-root',
  imports: [Clientes],
  styleUrl: './app.css',
  templateUrl: './app.html',
})
export class App {}
