import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EtudiantCard } from './etudiant-card';

describe('EtudiantCard', () => {
  let component: EtudiantCard;
  let fixture: ComponentFixture<EtudiantCard>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [EtudiantCard],
    }).compileComponents();

    fixture = TestBed.createComponent(EtudiantCard);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
