import React, { useState, useMemo } from 'react';
import { HelpCircle } from 'lucide-react';
import { formatCurrency } from '../../utils/formatting';
import Summary from '../Summary';

function TaxTab({ results, onOpenHelp }) {
  const [selectedYear, setSelectedYear] = useState(null);

  const yearlyData = useMemo(() => {
    if (!results?.results) return [];

    const transactions = results.results;
    const yearMap = {};

    transactions.forEach(tx => {
      if (tx.type === 'SELL' || tx.type === 'TRADE') {
        const year = tx.tax_year;
        if (!year) return;

        if (!yearMap[year]) {
          yearMap[year] = {
            year,
            total_gains: 0,
            total_tax: 0,
            transactions: 0,
            coin_breakdown: {}
          };
        }

        yearMap[year].total_gains += tx.capital_gain || 0;
        yearMap[year].total_tax += tx.total_tax || 0;
        yearMap[year].transactions += 1;

        const coin = tx.type === 'SELL' ? tx.coin : tx.from_coin;
        if (!yearMap[year].coin_breakdown[coin]) {
          yearMap[year].coin_breakdown[coin] = {
            gain_loss: 0,
            proceeds: 0,
            base_cost: 0,
            transactions: 0
          };
        }

        yearMap[year].coin_breakdown[coin].gain_loss += tx.capital_gain || 0;
        yearMap[year].coin_breakdown[coin].proceeds += (tx.amount * tx.price) || 0;
        if (tx.matched_buys) {
          tx.matched_buys.forEach(buy => {
            yearMap[year].coin_breakdown[coin].base_cost += buy.cost || 0;
          });
        }
        yearMap[year].coin_breakdown[coin].transactions += 1;
      }
    });

    return Object.values(yearMap).sort((a, b) => b.year - a.year);
  }, [results]);

  const totalTaxAllYears = useMemo(() => {
    return yearlyData.reduce((sum, y) => sum + (y.total_tax || 0), 0);
  }, [yearlyData]);
  const CapitalGain = formatCurrency(yearlyData.reduce((sum, y) => sum + y.total_gains, 0));

  if (!results) return null;

  return (
    <>
      <div className="tab-content">
        <div className="card">
          <div className="summary-component-header">
            <div className="header-with-help">
              <h2>Tax Summary</h2>
              <button type="button" className="btn-icon help" onClick={onOpenHelp} title="How to use this calculator">
                <HelpCircle size={20} />
              </button>
            </div>
            <p className="summary-component-subtitle">
              SARS tax-year runs from 1 March to 28/29 February
            </p>
          </div>

          <div className="summary-grid">
            {yearlyData.map((summary, index) => (
              <div
                className="summary-component-card"
                key={index}
                onClick={() => setSelectedYear(summary)}
              >
                <div className="summary-component-header">
                  <p>
                    <strong>{summary.year} Tax Year</strong>{' '}
                    <span>
                      1 Mar {summary.year - 1} - 28 Feb {summary.year}
                    </span>
                  </p>
                </div>
                <div className="summary-component-overall">
                  <h4>Capital Gain / Loss per Coin</h4>
                  {Object.entries(summary.coin_breakdown).map(([coin, data]) => (
                    <p
                      key={coin}
                      style={{
                        color: data.gain_loss < 0 ? '#ef4444' : '#4dc951e6'
                      }}
                    >
                      {coin}: {formatCurrency(data.gain_loss)}
                    </p>
                  ))}
                </div>
                <div className="summary-component-total-amount">
                  <h4>Total Capital Gain / Loss</h4>
                  <p
                    style={{
                      color: summary.total_gains < 0 ? '#ef4444' : '#4dc951e6'
                    }}
                  >
                    {formatCurrency(summary.total_gains)}
                  </p>
                </div>
                <div className="summary-component-tax">
                  <h4>Tax Due</h4>
                  <p style={{ color: '#2563eb' }}>
                    {formatCurrency(summary.total_tax)}
                  </p>
                </div>
              </div>
            ))}
          </div>

          {/* ---------- BOTTOM SECTION: TOTAL LIABILITY + PER‑YEAR TABLE + NOTES ---------- */}
          <div style={{ marginTop: '32px' }}>
            {/* Total Tax Liability (All Years) */}
            <div style={{ padding: '24px', backgroundColor: '#f9fafb', borderRadius: '8px', border: '1px solid #e5e7eb' }}>
              <h3 style={{ margin: '0 0 8px 0', fontSize: '1.5rem', fontWeight: 600 }}>Total Tax Liability (All Years)</h3>
              <div style={{ fontSize: '2rem', fontWeight: 700, color: '#2563eb', marginBottom: '8px' }}>
                {formatCurrency(totalTaxAllYears)}
              </div>
              <p style={{ margin: 0, color: '#6b7280', fontSize: '0.95rem' }}>
                This is your estimated total tax due across all tax years
              </p>
            </div>

            {/* Transparency Table: Per‑Year Tax Breakdown */}
            <div style={{ marginTop: '24px', padding: '24px', backgroundColor: '#ffffff', borderRadius: '8px', border: '1px solid #e5e7eb' }}>
              <h4 style={{ margin: '0 0 16px 0', fontSize: '1.1rem', fontWeight: 600 }}>Tax Paid per Tax Year</h4>
              <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                <thead>
                  <tr style={{ borderBottom: '2px solid #e5e7eb', textAlign: 'left' }}>
                    <th style={{ padding: '8px 0', fontSize: '0.9rem', fontWeight: 600, color: '#374151' }}>Tax Year</th>
                    <th style={{ padding: '8px 0', fontSize: '0.9rem', fontWeight: 600, color: '#374151' }}>Capital Gain</th>
                    <th style={{ padding: '8px 0', fontSize: '0.9rem', fontWeight: 600, color: '#374151' }}>Tax Paid</th>
                    <th style={{ padding: '8px 0', fontSize: '0.9rem', fontWeight: 600, color: '#374151' }}>Transactions</th>
                  </tr>
                </thead>
                <tbody>
                  {yearlyData.map(year => (
                    <tr key={year.year} style={{ borderBottom: '1px solid #f3f4f6' }}>
                      <td style={{ padding: '12px 0', fontSize: '0.95rem' }}>{year.year}</td>
                      <td style={{ padding: '12px 0', fontSize: '0.95rem', color: year.total_gains >= 0 ? '#059669' : '#dc2626' }}>
                        {formatCurrency(year.total_gains)}
                      </td>
                      <td style={{ padding: '12px 0', fontSize: '0.95rem', fontWeight: 500, color: '#2563eb' }}>
                        {formatCurrency(year.total_tax)}
                      </td>
                      <td style={{ padding: '12px 0', fontSize: '0.95rem', color: '#6b7280' }}>
                        {year.transactions}
                      </td>
                    </tr>
                  ))}
                </tbody>
                <tfoot>

                  <tr style={{ borderTop: '2px solid #e5e7eb', fontWeight: 600 }}>
                    <td style={{ padding: '12px 0', fontSize: '1rem' }}>Total</td>
                    <td style={{ padding: '12px 0', fontSize: '1rem', color: CapitalGain.includes('-') ? '#dc2626' : '#059669' }}>
                      {CapitalGain}
                    </td>
                    <td style={{ padding: '12px 0', fontSize: '1rem', color: '#2563eb' }}>
                      {formatCurrency(totalTaxAllYears)}
                    </td>
                    <td style={{ padding: '12px 0', fontSize: '1rem', color: '#6b7280' }}>
                      {yearlyData.reduce((sum, y) => sum + y.transactions, 0)}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>

            {/* Important Tax Notes */}
            <div style={{ marginTop: '24px', padding: '24px', backgroundColor: '#f9fafb', borderRadius: '8px', border: '1px solid #e5e7eb' }}>
              <h3 style={{ margin: '0 0 16px 0', fontSize: '1.25rem', fontWeight: 600 }}>Important Tax Notes</h3>
              <ul style={{ margin: 0, paddingLeft: '20px', color: '#4b5563' }}>
                <li style={{ marginBottom: '8px' }}>
                  This calculation uses the <strong>SARS-required FIFO method</strong>
                </li>
                <li style={{ marginBottom: '8px' }}>
                  Annual exclusion of R40,000 is applied per tax year
                </li>
                <li style={{ marginBottom: '8px' }}>
                  Long-term holdings (≥3 years) receive preferential tax treatment
                </li>
                <li style={{ marginBottom: '8px' }}>
                  Consult a tax professional for your final tax return
                </li>
                <li style={{ marginBottom: '8px' }}>
                  Keep all transaction records for 5 years as per SARS requirements
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      {selectedYear && (
        <Summary
          yearData={selectedYear}
          taxParameters={results.tax_parameters}
          setShow={() => setSelectedYear(null)}
        />
      )}
    </>
  );
}

export default TaxTab;