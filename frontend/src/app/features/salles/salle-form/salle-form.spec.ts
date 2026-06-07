import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SalleForm } from './salle-form';

describe('SalleForm', () => {
  let component: SalleForm;
  let fixture: ComponentFixture<SalleForm>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SalleForm],
    }).compileComponents();

    fixture = TestBed.createComponent(SalleForm);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
