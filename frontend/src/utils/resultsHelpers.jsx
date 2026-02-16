import React from 'react';
import { formatCurrency, formatNumber } from './formatting';

export const getTransactionDetails = (result) => {
  switch (result.type) {
    case 'BUY':
      return `Bought ${formatNumber(result.amount)} ${result.coin} at ${formatCurrency(result.price)} each`;
    case 'SELL':
      return `Sold ${formatNumber(result.amount)} ${result.coin} at ${formatCurrency(result.price)} each`;
    case 'TRADE':
      return `Traded ${formatNumber(result.amount)} ${result.from_coin} for ${formatNumber(result.received_amount || 0)} ${result.to_coin}`;
    case 'TRANSFER':
      return `Transferred ${formatNumber(result.amount)} ${result.coin}`;
    default:
      return '';
  }
};

export const renderStepByStepMath = (result) => {
  if (!result.matched_buys || result.matched_buys.length === 0) return null;

  return (
    <div className="step-by-step">
      <h4>Step-by-Step Calculation:</h4>
      {result.matched_buys.map((buy, index) => {
        // Use the figures provided by the backend
        const cost = buy.cost || 0;
        const proceeds = buy.proceeds || 0;
        const fee = buy.fee || 0;
        const netProceeds = buy.net_proceeds || 0;
        const gain = buy.gain || 0;
        const feeRate = (buy.fee_rate || 0) * 100; // Convert to percentage
        
        // Calculate annual exclusion for this specific lot
        let taxableGain = buy.taxable_gain || 0;
        const remainingExclusion = buy.remaining_exclusion || 0;
        
        // Apply annual exclusion if available
        let gainAfterExclusion = gain;
        if (remainingExclusion > 0) {
          gainAfterExclusion = Math.max(0, gain - remainingExclusion);
        }

        return (
          <div key={index} className="calculation-step">
            <div className="step-number">Step {index + 1}</div>
            <div className="step-details">
              <p>
                <strong>Cost Basis:</strong> {formatNumber(buy.amount_sold)} × {formatCurrency(buy.buy_price)} = {formatCurrency(cost)}
              </p>
              <p>
                <strong>Sale Proceeds:</strong> {formatNumber(buy.amount_sold)} × {formatCurrency(result.price)} = {formatCurrency(proceeds)}
              </p>
              {fee > 0 && (
                <p>
                  <strong>Transaction Fee:</strong> {feeRate.toFixed(2)}% = {formatCurrency(fee)}
                </p>
              )}
              <p>
                <strong>Net Proceeds:</strong> {formatCurrency(proceeds)} − {formatCurrency(fee)} = {formatCurrency(netProceeds)}
              </p>
              <p>
                <strong>Capital Gain:</strong> {formatCurrency(netProceeds)} − {formatCurrency(cost)} = {formatCurrency(gain)}
              </p>
              {remainingExclusion > 0 && gain > 0 && (
                <p>
                  <strong>Annual Exclusion:</strong> R{remainingExclusion.toLocaleString()} applied
                </p>
              )}
              <p>
                <strong>Taxable Gain:</strong> {formatCurrency(taxableGain)}
              </p>
              <p>
                <strong>Holding Period:</strong> {typeof buy.holding_years === 'number' ? buy.holding_years.toFixed(1) : '0'} years
                {buy.is_long_term && <span className="long-term-badge"> (Long-term)</span>}
              </p>
              <p>
                <strong>Tax Rate:</strong> {buy.is_long_term ? '10% (Long-term)' : '18% (Short-term)'}
              </p>
              <p>
                <strong>Tax Amount:</strong> {formatCurrency(buy.tax_amount || 0)}
              </p>
            </div>
          </div>
        );
      })}
    </div>
  );
};