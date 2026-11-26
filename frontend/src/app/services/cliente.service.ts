import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Cliente {
  id: number;
  razon_social: string;
  documento_identificacion: string;
  direccion: string;
  telefono: string;
  correo: string;
  created_at?: string;
}

@Injectable({
  providedIn: 'root',
})
export class ClienteService {
  private http = inject(HttpClient);
  private apiUrl = 'http://127.0.0.1:8088/index.php';

  listar(): Observable<Cliente[]> {
    return this.http.get<Cliente[]>(this.apiUrl + '?action=listar');
  }

  crear(c: any): Observable<{ ok: boolean; id: number }> {
    return this.http.post<{ ok: boolean; id: number }>(this.apiUrl + '?action=insertar', c);
  }

  actualizar(c: any): Observable<{ ok: boolean; id: number }> {
    return this.http.post<{ ok: boolean; id: number }>(this.apiUrl + '?action=actualizar', c);
  }

  eliminar(id: number): Observable<{ ok: boolean; id: number }> {
    return this.http.post<{ ok: boolean; id: number }>(this.apiUrl + '?action=eliminar', { id });
  }
}
