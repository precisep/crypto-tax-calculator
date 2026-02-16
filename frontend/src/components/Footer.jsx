import React from 'react'
import { Calculator } from 'lucide-react'
import logo from '../assets/iconLogo.png'

function Footer() {
  return (
    <>
      <footer className="footer">
        <div className="footer-content">
          <div className="footer-left">
            <div className="footer-logo">
              <Calculator size={24} />
              <div>
                <p className="footer-title">TaxTim Crypto Tax Calculator</p>
                <p className="footer-tagline">Official SARS FIFO Calculations</p>
              </div>
            </div>
            <p className="footer-disclaimer">
              This tool provides estimates for informational purposes only. 
              Consult a tax professional for official tax advice. Calculations use 
              SARS-required FIFO method with R40,000 annual exclusion.
            </p>
          </div>
          <div className="footer-right">
            <div className="footer-partner">
                <div className="partner-badge">
                  <div className="logo-icon">
                    <img src={logo} alt="Taxim icon" className="logo-img" />
                    <strong className="logo-text">TAXTIM</strong>
                  </div>
                    <span className="partner-text">Official Partner</span>
                </div>
                  <p>Developed for South African taxpayers</p>
            </div>
            <p className="footer-copyright">
              © 2024 TaxTim Crypto Tax Calculator. All rights reserved.
            </p>
            <p className="footer-version">
              Version 1.0 • SARS Compliant • FIFO Method
            </p>
          </div>
        </div>
      </footer>
    </>
  )
}

export default Footer