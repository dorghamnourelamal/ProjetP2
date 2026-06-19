import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PersonnelCard } from './personnel-card';

describe('PersonnelCard', () => {
  let component: PersonnelCard;
  let fixture: ComponentFixture<PersonnelCard>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PersonnelCard],
    }).compileComponents();

    fixture = TestBed.createComponent(PersonnelCard);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
