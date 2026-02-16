import { X } from 'lucide-react';
import { formatCurrency } from '../utils/formatting';

const Summary = ({ yearData, taxParameters, setShow }) => {
  if (!yearData) return null;

  const year = yearData.year;
  const annualExclusion = taxParameters?.annual_exclusion || 40000;
  const shortTermRate = taxParameters?.short_term_rate || 18;

  return (
    <div className="summary-modal-overlay" onClick={setShow}>
      <div className="summary-modal" onClick={e => e.stopPropagation()}>
        <div className="summary-modal-card">
          <div className="tax-header">
            <div className="text">
              <h2>Tax Year Summary</h2>
              <p className="tax-subtitle">
                SARS tax year runs from 1 March to 28/29 February
              </p>
            </div>
            <div className="cross-icon">
              <X size={20} onClick={setShow} />
            </div>
          </div>

          <div className="tax-summary">
            <div className="year-card">
              <div className="year-header">
                <div>
                  <h3>Tax Year {year}</h3>
                  <span className="year-period">
                    1 March {year - 1} - 28 Feb {year}
                  </span>
                </div>
                <div className="year-tax">
                  <span className="tax-label">Tax Due:</span>
                  <span className="tax-amount">
                    {formatCurrency(yearData.total_tax)}
                  </span>
                </div>
              </div>

              {Object.keys(yearData.coin_breakdown).length > 0 && (
                <div className="coin-breakdown">
                  <h4>Per Coin Breakdown</h4>
                  <div className="coin-grid">
                    {Object.entries(yearData.coin_breakdown).map(([coin, data]) => (
                      <div key={coin} className="coin-card">
                        <div className="coin-header">
                          <span className="coin-name">{coin}</span>
                          <span
                            className={`coin-gain ${
                              data.gain_loss >= 0 ? 'positive' : 'negative'
                            }`}
                          >
                            {formatCurrency(data.gain_loss)}
                          </span>
                        </div>
                        <div className="coin-details">
                          <div className="coin-detail">
                            <span>Proceeds:</span>
                            <span>{formatCurrency(data.proceeds || 0)}</span>
                          </div>
                          <div className="coin-detail">
                            <span>Base Cost:</span>
                            <span>{formatCurrency(data.base_cost || 0)}</span>
                          </div>
                          <div className="coin-detail">
                            <span>Transactions:</span>
                            <span>{data.transactions || 0}</span>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              <div className="year-details">
                <div className="year-row">
                  <span>Total Capital Gains:</span>
                  <strong
                    className={yearData.total_gains >= 0 ? 'positive' : 'negative'}
                  >
                    {formatCurrency(yearData.total_gains)}
                  </strong>
                </div>
                <div className="year-row">
                  <span>Annual Exclusion:</span>
                  <strong>- {formatCurrency(annualExclusion)}</strong>
                </div>
                <div className="year-row highlight">
                  <span>Taxable Gain:</span>
                  <strong>
                    {formatCurrency(
                      Math.max(0, yearData.total_gains - annualExclusion)
                    )}
                  </strong>
                </div>
                <div className="year-row">
                  <span>Average Tax Rate:</span>
                  <strong>
                    {yearData.total_gains > 0
                      ? `${((yearData.total_tax / yearData.total_gains) * 100).toFixed(
                          1
                        )}%`
                      : '0%'}
                  </strong>
                </div>
                <div className="year-row">
                  <span>Taxable Transactions:</span>
                  <strong>{yearData.transactions}</strong>
                </div>
              </div>
            </div>
          </div>

          {taxParameters && (
            <div className="tax-parameters">
              <h3>SARS Tax Parameters Applied</h3>
              <div className="parameters-grid">
                <div className="parameter">
                  <span>Annual Exclusion</span>
                  <div className="parameter-value">
                    <strong>{formatCurrency(annualExclusion)}</strong>
                    <span className="parameter-desc">Per tax year</span>
                  </div>
                </div>
                <div className="parameter">
                  <span>Short-term Rate</span>
                  <div className="parameter-value">
                    <strong>{shortTermRate}%</strong>
                    <span className="parameter-desc">Assets held &lt;3 years</span>
                  </div>
                </div>
                <div className="parameter">
                  <span>Long-term Rate</span>
                  <div className="parameter-value">
                    <strong>{taxParameters.long_term_rate}%</strong>
                    <span className="parameter-desc">Assets held ≥3 years</span>
                  </div>
                </div>
                <div className="parameter">
                  <span>Long-term Threshold</span>
                  <div className="parameter-value">
                    <strong>{taxParameters.long_term_threshold_years} years</strong>
                    <span className="parameter-desc">SARS requirement</span>
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default Summary;