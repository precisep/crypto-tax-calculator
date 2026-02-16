import { Wallet, Coins, HelpCircle } from 'lucide-react';
import { formatCurrency, formatNumber } from '../../utils/formatting';

const BalancesTab = ({ results, onOpenHelp }) => {
  if (!results) {
    return (
      <div className="tab-content">
        <div className="card">
          <div className="empty-state">
            <Wallet size={48} />
            <h4>No results available</h4>
            <p>Please run a calculation first</p>
          </div>
        </div>
      </div>
    );
  }

  let balances = [];
  
  if (results.data && Array.isArray(results.data.balances)) {
    balances = results.data.balances;
  } else if (Array.isArray(results.balances)) {
    balances = results.balances;
  } else if (Array.isArray(results)) {
    balances = results;
  }

  if (balances.length === 0) {
    return (
      <div className="tab-content">
        <div className="card">
          <div className="balances-header">
            <div className="header-with-help">
              <h2>Crypto Balances</h2>
              <button type="button" className="btn-icon help" onClick={onOpenHelp} title="How to use this calculator">
                <HelpCircle size={20} />
              </button>
            </div>
            <p className="balances-subtitle">Current holdings after all transactions</p>
          </div>
          <div className="empty-state">
            <Wallet size={48} />
            <h4>No crypto holdings remaining</h4>
            <p>All crypto has been sold or there were no buy transactions</p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="tab-content">
      <div className="card">
        <div className="balances-header">
          <div className="header-with-help">
            <h2>Crypto Balances</h2>
            <button type="button" className="btn-icon help" onClick={onOpenHelp} title="How to use this calculator">
              <HelpCircle size={20} />
            </button>
          </div>
          <p className="balances-subtitle">Current holdings after all transactions</p>

          <div className="portfolio-summary">
            <div className="portfolio-total">
              <span className="portfolio-label">Total Holdings:</span>
              <span className="portfolio-value">{balances.length} Assets</span>
            </div>
          </div>
        </div>
        
        <div className="balances-grid">
          {balances.map((balance, index) => {
            const amount = balance.total_amount || 0;
            const baseCost = balance.base_cost || 0;
            const averagePrice = balance.average_price || (amount > 0 ? baseCost / amount : 0);
            const currentValue = amount * averagePrice;
            const gainLoss = currentValue - baseCost;
            
            return (
              <div key={index} className="balance-card">
                <div className="balance-header">
                  <div className="balance-coin">
                    <div className="coin-icon" style={{ background: getCoinColor(balance.coin) }}>
                      {balance.coin ? balance.coin.substring(0, 2) : '??'}
                    </div>
                    <div>
                      <h3>{balance.coin || 'Unknown'}</h3>
                      {balance.wallet && (
                        <span className="balance-wallet">
                          <Wallet size={12} /> {balance.wallet}
                        </span>
                      )}
                    </div>
                  </div>
                  <div className="balance-amount-large">
                    {formatNumber(amount, 4)} {balance.coin}
                  </div>
                </div>
                
                <div className="balance-details">
                  <div className="detail-row">
                    <span><Coins size={14} /> Amount:</span>
                    <strong>{formatNumber(amount, 4)} {balance.coin}</strong>
                  </div>
                  <div className="detail-row">
                    <span>Cost Basis:</span>
                    <strong>{formatCurrency(baseCost)}</strong>
                  </div>
                  <div className="detail-row">
                    <span>Average Price:</span>
                    <strong>{formatCurrency(averagePrice)}</strong>
                  </div>
                  <div className="detail-row">
                    <span>Current Value:</span>
                    <strong>{formatCurrency(currentValue)}</strong>
                  </div>
                  <div className="detail-row">
                    <span>Unrealized {gainLoss >= 0 ? 'Gain' : 'Loss'}:</span>
                    <strong className={gainLoss >= 0 ? 'positive' : 'negative'}>
                      {formatCurrency(Math.abs(gainLoss))}
                    </strong>
                  </div>
                </div>
                
                <div className="balance-stats">
                  <div className="stat-badge">
                    <span className="stat-label">Status</span>
                    <span className="stat-value active">Active</span>
                  </div>
                  <div className="stat-badge">
                    <span className="stat-label">Wallet</span>
                    <span className="stat-value">{balance.wallet || 'Default'}</span>
                  </div>
                </div>
              </div>
            );
          })}
        </div>
        
        <div className="portfolio-totals">
          <h3>Portfolio Summary</h3>
          <div className="totals-grid">
            <div className="total-item">
              <span>Total Assets:</span>
              <strong>{balances.length}</strong>
            </div>
            <div className="total-item">
              <span>Total Cost Basis:</span>
              <strong>{formatCurrency(
                balances.reduce((sum, b) => sum + (b.base_cost || 0), 0)
              )}</strong>
            </div>
            <div className="total-item">
              <span>Total Holdings:</span>
              <strong>{formatNumber(
                balances.reduce((sum, b) => sum + (b.total_amount || 0), 0),
                4
              )}</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

const getCoinColor = (coin) => {
  const colors = {
    BTC: 'linear-gradient(135deg, #f7931a 0%, #f2a900 100%)',
    ETH: 'linear-gradient(135deg, #627eea 0%, #3c55b7 100%)',
    SOL: 'linear-gradient(135deg, #00ffa3 0%, #03e1ff 100%)',
    default: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
  };
  return colors[coin] || colors.default;
};

export default BalancesTab;