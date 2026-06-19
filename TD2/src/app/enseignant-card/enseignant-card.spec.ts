import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EnseignantCard } from './enseignant-card';

describe('EnseignantCard', () => {
  let component: EnseignantCard;
  let fixture: ComponentFixture<EnseignantCard>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [EnseignantCard],
    }).compileComponents();

    fixture = TestBed.createComponent(EnseignantCard);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
