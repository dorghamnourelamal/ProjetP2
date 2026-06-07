import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SalleList } from './salle-list';

describe('SalleList', () => {
  let component: SalleList;
  let fixture: ComponentFixture<SalleList>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SalleList],
    }).compileComponents();

    fixture = TestBed.createComponent(SalleList);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
