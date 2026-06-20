import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth-guard';
import { roleGuard } from './core/guards/role-guard';

import { Home } from './features/home/home/home';

import { EventList } from './features/events/event-list/event-list';
import { EventDetail } from './features/events/event-detail/event-detail';
import { EventForm } from './features/events/event-form/event-form';

import { SalleList } from './features/salles/salle-list/salle-list';
import { SalleForm } from './features/salles/salle-form/salle-form';

import { ReservationList } from './features/reservations/reservation-list/reservation-list';
import { ReservationForm } from './features/reservations/reservation-form/reservation-form';

import { TicketList } from './features/tickets/ticket-list/ticket-list';
import { TicketForm } from './features/tickets/ticket-form/ticket-form';
import { TicketVerify } from './features/tickets/ticket-verify/ticket-verify';

import { Dashboard } from './features/dashboard/dashboard/dashboard';
import { Login } from './features/auth/login/login';
import { Register } from './features/auth/register/register';
import { ForgotPassword } from './features/auth/forgot-password/forgot-password';
import { ResetPassword } from './features/auth/reset-password/reset-password';
import { Forbidden } from './shared/components/forbidden/forbidden';
import { NotFound } from './shared/components/not-found/not-found';

export const routes: Routes = [
  { path: '', component: Home, pathMatch: 'full' },

  { path: 'login', component: Login },
  { path: 'register', component: Register },
  { path: 'forgot-password', component: ForgotPassword },
  { path: 'reset-password', component: ResetPassword },

  { path: 'events', component: EventList },
  { path: 'events/add', component: EventForm, canActivate: [roleGuard(['admin'])] },
  { path: 'events/:id', component: EventDetail },
  { path: 'events/:id/edit', component: EventForm, canActivate: [roleGuard(['admin'])] },

  { path: 'events/:id/reserve', component: ReservationForm, canActivate: [authGuard] },

  { path: 'salles', component: SalleList, canActivate: [roleGuard(['admin'])] },
  { path: 'salles/add', component: SalleForm, canActivate: [roleGuard(['admin'])] },
  { path: 'salles/:id/edit', component: SalleForm, canActivate: [roleGuard(['admin'])] },

  { path: 'reservations', component: ReservationList, canActivate: [authGuard] },

  { path: 'tickets/verify/:code', component: TicketVerify },

  { path: 'tickets', component: TicketList, canActivate: [roleGuard(['admin'])] },
  { path: 'tickets/:id/edit', component: TicketForm, canActivate: [roleGuard(['admin'])] },

  { path: 'dashboard', component: Dashboard, canActivate: [roleGuard(['admin'])] },

  { path: 'forbidden', component: Forbidden },
  { path: '**', component: NotFound },
];
