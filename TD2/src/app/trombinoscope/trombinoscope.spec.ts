import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Trombinoscope } from './trombinoscope';

describe('Trombinoscope', () => {
  let component: Trombinoscope;
  let fixture: ComponentFixture<Trombinoscope>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Trombinoscope],
    }).compileComponents();

    fixture = TestBed.createComponent(Trombinoscope);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
