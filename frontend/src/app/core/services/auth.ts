import { Injectable, signal, computed } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { environment } from '../../../environments/environment';
import { AuthResponse, LoginPayload, RegisterPayload, User } from '../models/user.model';

const TOKEN_KEY = 'auth_token';
const USER_KEY = 'auth_user';

/**
 * Service central d'authentification : consomme l'API Sanctum (token Bearer),
 * conserve la session courante dans des signaux et la persiste dans sessionStorage
 * pour survivre à un rafraîchissement de page.
 */
@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly apiUrl = `${environment.apiUrl}/auth`;

  private readonly currentUserSignal = signal<User | null>(this.restoreUser());
  private readonly tokenSignal = signal<string | null>(this.restoreToken());

  /** Utilisateur courant (ou null si non connecté), exposé en lecture seule. */
  readonly currentUser = this.currentUserSignal.asReadonly();

  /** Vrai si un utilisateur est authentifié. */
  readonly isAuthenticated = computed(() => this.tokenSignal() !== null);

  /** Vrai si l'utilisateur courant possède le rôle "admin". */
  readonly isAdmin = computed(() => this.currentUserSignal()?.role === 'admin');

  constructor(private http: HttpClient) {}

  register(payload: RegisterPayload): Observable<AuthResponse> {
    return this.http
      .post<AuthResponse>(`${this.apiUrl}/register`, payload)
      .pipe(tap((res) => this.handleAuthSuccess(res)));
  }

  login(payload: LoginPayload): Observable<AuthResponse> {
    return this.http
      .post<AuthResponse>(`${this.apiUrl}/login`, payload)
      .pipe(tap((res) => this.handleAuthSuccess(res)));
  }

  logout(): Observable<{ message: string }> {
    return this.http
      .post<{ message: string }>(`${this.apiUrl}/logout`, {})
      .pipe(tap(() => this.clearSession()));
  }

  /** Récupère le profil de l'utilisateur connecté depuis l'API (rafraîchissement de session). */
  fetchCurrentUser(): Observable<User> {
    return this.http.get<User>(`${this.apiUrl}/me`).pipe(
      tap((user) => {
        this.currentUserSignal.set(user);
        this.persistUser(user);
      }),
    );
  }

  getToken(): string | null {
    return this.tokenSignal();
  }

  /** Force la déconnexion locale (ex : après une réponse 401 de l'API). */
  clearSession(): void {
    this.currentUserSignal.set(null);
    this.tokenSignal.set(null);
    sessionStorage.removeItem(TOKEN_KEY);
    sessionStorage.removeItem(USER_KEY);
  }

  private handleAuthSuccess(res: AuthResponse): void {
    this.currentUserSignal.set(res.user);
    this.tokenSignal.set(res.token);
    sessionStorage.setItem(TOKEN_KEY, res.token);
    this.persistUser(res.user);
  }

  private persistUser(user: User): void {
    sessionStorage.setItem(USER_KEY, JSON.stringify(user));
  }

  private restoreToken(): string | null {
    return sessionStorage.getItem(TOKEN_KEY);
  }

  private restoreUser(): User | null {
    const raw = sessionStorage.getItem(USER_KEY);
    return raw ? (JSON.parse(raw) as User) : null;
  }
}
