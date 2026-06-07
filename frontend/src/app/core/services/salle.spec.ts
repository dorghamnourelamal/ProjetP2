import { TestBed } from '@angular/core/testing';

import { Salle } from './salle';

describe('Salle', () => {
  let service: Salle;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(Salle);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
