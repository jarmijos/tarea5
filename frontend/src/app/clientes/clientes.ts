import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ClienteService } from '../services/cliente.service';

@Component({
  selector: 'app-clientes',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './clientes.html',
  styleUrl: './clientes.css',
})
export class Clientes implements OnInit {
  private fb = inject(FormBuilder);
  private service = inject(ClienteService);

  clientes: any[] = [];
  editingId: number | null = null;
  mensaje: string | null = null;
  form = this.fb.group({
    razon_social: ['', Validators.required],
    documento_identificacion: ['', Validators.required],
    direccion: ['', Validators.required],
    telefono: ['', Validators.required],
    correo: ['', Validators.required],
  });

  ngOnInit(): void {
    this.cargar();
  }

  cargar(): void {
    this.service.listar().subscribe((data: any[]) => (this.clientes = data));
  }

  guardar(): void {
    if (this.form.invalid) {
      this.mensaje = 'Por favor complete todos los campos obligatorios';
      return;
    }
    const c: any = this.form.value;
    if (this.editingId) {
      this.service.actualizar({ ...c, id: this.editingId }).subscribe(() => {
        this.editingId = null;
        this.form.reset();
        this.cargar();
        this.mensaje = 'Cliente actualizado correctamente';
      });
    } else {
      this.service.crear(c).subscribe(() => {
        this.form.reset();
        this.cargar();
        this.mensaje = 'Cliente ingresado correctamente';
      });
    }
  }

  editar(c: any): void {
    this.editingId = c.id;
    this.form.setValue({
      razon_social: c.razon_social,
      documento_identificacion: c.documento_identificacion,
      direccion: c.direccion,
      telefono: c.telefono,
      correo: c.correo,
    });
  }

  cancelar(): void {
    this.editingId = null;
    this.form.reset();
  }

  eliminar(id: number): void {
    this.service.eliminar(id).subscribe(() => this.cargar());
  }
}
