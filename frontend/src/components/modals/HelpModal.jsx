import React from 'react';
import {
  HelpCircle,
  X,
  FileSpreadsheet,
  TrendingUp,
  Percent,
  Receipt,
  Calculator,
  ListOrdered,
  Download,
  Wallet,
  Coins,
  BarChart3,
  Calendar,
  PieChart,
} from 'lucide-react';

const UPLOAD_CONTENT = (
  <>
    <div className="help-modal-intro">
      Upload your crypto transaction data to calculate SARS-compliant capital gains tax. Follow the steps below.
    </div>
    <div className="help-modal-grid">
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--file">
          <FileSpreadsheet size={24} />
        </div>
        <h4>Supported file formats</h4>
        <p>
          Upload <strong>Excel</strong>, <strong>CSV</strong>, or paste <strong>JSON</strong> from exchanges like Luno, Binance, VALR. Use “Download Template” for the correct column layout.
        </p>
      </div>
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--chart">
          <TrendingUp size={24} />
        </div>
        <h4>How it works</h4>
        <p>
          SARS requires <strong>FIFO</strong> (First In, First Out). We track purchase prices and calculate capital gains when you sell or trade. Each disposal is matched to the earliest acquisition.
        </p>
      </div>
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--percent">
          <Percent size={24} />
        </div>
        <h4>SARS tax rates</h4>
        <p>
          <strong>Short-term</strong> (held &lt;3 years): 18% • <strong>Long-term</strong> (≥3 years): 10% • <strong>Annual exclusion</strong>: R40,000 per tax year.
        </p>
      </div>
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--receipt">
          <Receipt size={24} />
        </div>
        <h4>Transaction fees</h4>
        <p>
          Enter fees to reduce taxable gains. Use decimal form (e.g. <strong>0.015</strong> = 1.5%). Fee coin is usually ZAR.
        </p>
      </div>
    </div>
    <div className="help-modal-fields">
      <h4>Required fields</h4>
      <p>
        <code>date</code>, <code>coin</code>, <code>type</code>, <code>amount</code>, <code>price</code>, <code>wallet</code>, <code>fee</code>, <code>fee_coin</code>
      </p>
    </div>
  </>
);

const RESULTS_CONTENT = (
  <>
    <div className="help-modal-intro">
      This tab shows the outcome of your SARS FIFO calculation: each taxable event (sell or trade), the capital gain or loss, and the tax due.
    </div>
    <div className="help-modal-grid">
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--file">
          <Calculator size={24} />
        </div>
        <h4>Summary cards at the top</h4>
        <p>
          <strong>Total Capital Gain</strong> is the sum of all gains (or losses) from sells and trades. <strong>Total Tax Due</strong> is your estimated tax. <strong>Total Fees</strong> are the transaction fees used in the calculation. <strong>Transactions</strong> is how many events were processed.
        </p>
      </div>
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--chart">
          <ListOrdered size={24} />
        </div>
        <h4>Result rows</h4>
        <p>
          Each row is one taxable event (sell or trade). <strong>Expand a row</strong> to see the step‑by‑step FIFO match: proceeds, cost basis, capital gain, tax rate (short‑ or long‑term), and which buy lots were used.
        </p>
      </div>
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--percent">
          <TrendingUp size={24} />
        </div>
        <h4>Gains and tax</h4>
        <p>
          <strong>Green</strong> values are gains; <strong>red</strong> are losses. Tax is applied to net gains after the annual exclusion. Short‑term (held &lt;3 years) uses the higher rate; long‑term uses the lower rate.
        </p>
      </div>
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--receipt">
          <Download size={24} />
        </div>
        <h4>Export Full Report</h4>
        <p>
          Use <strong>Export Full Report</strong> to download a CSV of all results for your records or for a tax professional.
        </p>
      </div>
    </div>
  </>
);

const BALANCES_CONTENT = (
  <>
    <div className="help-modal-intro">
      This tab shows your <strong>current crypto holdings</strong> after all transactions have been applied—i.e. what you still own, not what you’ve already sold or traded.
    </div>
    <div className="help-modal-grid">
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--file">
          <Wallet size={24} />
        </div>
        <h4>Balance cards</h4>
        <p>
          Each card is one asset (e.g. BTC, ETH). You see <strong>amount</strong> held, <strong>wallet</strong> (if applicable), and the main totals for that coin.
        </p>
      </div>
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--chart">
          <Coins size={24} />
        </div>
        <h4>Cost basis and value</h4>
        <p>
          <strong>Cost basis</strong> is what you paid (FIFO-acquired). <strong>Average price</strong> is total cost ÷ quantity. <strong>Current value</strong> is amount × average price. <strong>Unrealized gain/loss</strong> is the difference between current value and cost basis—not taxed until you sell.
        </p>
      </div>
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--percent">
          <PieChart size={24} />
        </div>
        <h4>Portfolio summary</h4>
        <p>
          At the bottom, <strong>Total Assets</strong> is the number of coins you hold. <strong>Total Cost Basis</strong> and <strong>Total Holdings</strong> sum across all balances.
        </p>
      </div>
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--receipt">
          <HelpCircle size={24} />
        </div>
        <h4>No holdings</h4>
        <p>
          If you’ve sold or traded everything, you’ll see an empty state. Run a calculation with buy (and optionally sell) transactions to see balances here.
        </p>
      </div>
    </div>
  </>
);

const TAX_CONTENT = (
  <>
    <div className="help-modal-intro">
      This tab summarises your <strong>tax by SARS tax year</strong>. The SARS tax year runs from <strong>1 March to 28/29 February</strong>. Use it to see how much tax is due per year and to prepare for filing.
    </div>
    <div className="help-modal-grid">
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--file">
          <Calendar size={24} />
        </div>
        <h4>Year cards</h4>
        <p>
          Each card is one tax year. It shows <strong>capital gain/loss per coin</strong>, <strong>total gain/loss</strong> for the year, and <strong>tax due</strong> for that year. <strong>Click a card</strong> to open a detail modal with a full breakdown and SARS parameters.
        </p>
      </div>
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--chart">
          <BarChart3 size={24} />
        </div>
        <h4>Total tax liability</h4>
        <p>
          The <strong>Total Tax Liability (All Years)</strong> section adds up tax due across all years. The table below lists each tax year with its capital gain and tax paid—useful for reconciliation and filing.
        </p>
      </div>
      <div className="help-modal-item">
        <div className="help-modal-item-icon help-modal-item-icon--percent">
          <Percent size={24} />
        </div>
        <h4>Green vs red</h4>
        <p>
          <strong>Green</strong> amounts are gains; <strong>red</strong> are losses. Tax is calculated on net gains after the annual exclusion (R40,000) per year.
        </p>
      </div>
    </div>
  </>
);

const VARIANT_CONFIG = {
  upload: {
    title: 'How to Use This Calculator',
    content: UPLOAD_CONTENT,
  },
  results: {
    title: 'Understanding Tax Calculation Results',
    content: RESULTS_CONTENT,
  },
  balances: {
    title: 'Understanding Crypto Balances',
    content: BALANCES_CONTENT,
  },
  tax: {
    title: 'Understanding the Tax Summary',
    content: TAX_CONTENT,
  },
};

const HelpModal = ({ variant = 'upload', onClose }) => {
  const config = VARIANT_CONFIG[variant] || VARIANT_CONFIG.upload;

  return (
    <div className="help-modal-overlay" onClick={onClose}>
      <div className="help-modal" onClick={e => e.stopPropagation()}>
        <div className="help-modal-header">
          <h2><HelpCircle size={24} /> {config.title}</h2>
          <button type="button" className="help-modal-close" onClick={onClose} aria-label="Close">
            <X size={22} />
          </button>
        </div>
        <div className="help-modal-body">{config.content}</div>
      </div>
    </div>
  );
};

export default HelpModal;
