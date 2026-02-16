import React, { useState } from 'react';
import { 
  HelpCircle, Upload, Download, FileText, FileJson, 
  FileSpreadsheet, FileSpreadsheet as ExcelIcon, Copy, Plus, Trash2, 
  Calendar, Coins, Wallet,
  ChevronDown, ChevronUp,
} from 'lucide-react';
import { downloadCSVTemplate } from '../../utils/export';
import CalculatorBtn from '../buttons/CalculatorBtn';
import ErrMsgInputTab from '../errs/ErrMsgInputTab';


const InputTab = ({
  showHelp,
  setShowHelp,
  uploadMode,
  setUploadMode,
  triggerFileInput,
  fileInputRef,
  handleFileUpload,
  loadExample,
  loading,
  transactions,
  inputText,
  setInputText,
  handleParse,
  addTransaction,
  updateTransaction,
  removeTransaction,
  handleCalculate,
  clearAllTransactions,
  error,
}) => {
  const [ showTextArea, setShowTextArea ] = useState(true);
  return (
    <div className="tab-content">
      <div className="card">
        <div className="card-header">
          <div className="card-content">
            <div className="card-header-content">
              <div className="header-with-help">
                {inputText ? <h2>Upload Your Crypto Transactions</h2> : <h2>No Crypto Transactions Yet</h2>}
                <button 
                  className="btn-icon help" 
                  onClick={() => setShowHelp('upload')}
                  title="How to use this calculator"
                >
                  <HelpCircle size={20} />
                </button>
              </div>
              <p className="card-subtitle">
                Upload your crypto transaction data to calculate SARS-compliant
                tax liability
              </p>
              {error && <ErrMsgInputTab error={error} />}
            </div>
            {inputText && (
              <CalculatorBtn
                handleCalculate={handleCalculate}
                loading={loading}
              />
            )}
          </div>
        </div>
        
        <div className="upload-section">
          <div className="upload-modes">
            <button
              className={`upload-mode-btn ${
                uploadMode === "excel" ? "active" : ""
              }`}
              onClick={() => triggerFileInput("excel")}
            >
              <ExcelIcon size={18} />
              Excel Upload
              <span className="mode-badge">Recommended</span>
            </button>
            <button
              className={`upload-mode-btn ${
                uploadMode === "csv" ? "active" : ""
              }`}
              onClick={() => triggerFileInput("csv")}
            >
              <FileText size={18} />
              CSV Upload
            </button>
            <button
              className={`upload-mode-btn ${
                uploadMode === "json" ? "active" : ""
              }`}
              onClick={() => setUploadMode("json")}
            >
              <FileJson size={18} />
              JSON Editor
            </button>
          </div>

          <div className="upload-actions">
            <button
              className="btn btn-secondary"
              onClick={() => triggerFileInput(uploadMode)}
            >
              <Upload size={18} />
              Upload File
            </button>

            <button className="btn btn-secondary" onClick={downloadCSVTemplate}>
              <Download size={18} />
              Download Template
            </button>

            <button className="btn btn-secondary" onClick={loadExample}>
              Load Example
            </button>
          </div>

          <div className="upload-info">
            <p>
              <strong>Required fields:</strong> date, coin, type, amount, price,
              wallet, fee, fee_coin
            </p>
            <p>
              Upload transaction history from your exchange or use the example
              data.
            </p>
          </div>
        </div>

        <input
          type="file"
          ref={fileInputRef}
          onChange={handleFileUpload}
          accept={
            uploadMode === "json"
              ? ".json"
              : uploadMode === "csv"
              ? ".csv"
              : ".xlsx,.xls"
          }
          style={{ display: "none" }}
        />

        {inputText && (
          <div className="input-section">
            <div className="input-header">
              <h3>Transaction Data ({transactions.length} transactions)</h3>
              <div className="input-header-actions">
                <button
                  className="btn btn-secondary btn-sm"
                  onClick={showTextArea ? () => setShowTextArea(false) : () => setShowTextArea(true)}
                >
                  { showTextArea ? <ChevronUp  size={18} /> : <ChevronDown size={18} /> }
                  { showTextArea ? 'Collapse' : 'Expand' }
                </button>
                <button
                  className="btn btn-secondary btn-sm"
                  onClick={handleParse}
                >
                  Parse JSON
                </button>
                <button
                  className="btn btn-secondary btn-sm"
                  onClick={() => navigator.clipboard.writeText(inputText)}
                >
                  <Copy size={16} />
                  Copy
                </button>
              </div>
            </div>
            { showTextArea ? 
              <textarea
                value={inputText}
                onChange={(e) => setInputText(e.target.value)}
                placeholder="Paste your transaction data here in JSON format..."
                rows={12}
                className="json-input"
              /> : ''
            }
          </div>
        )}

        <div className="table-container">
          <div className="table-header">
            <div>
              <h3>Transaction Preview</h3>
              <p className="table-subtitle">
                Review and edit transactions before calculation
              </p>
            </div>
            <div>
              <button
                className="btn btn-secondary btn-sm"
                onClick={addTransaction}
              >
                <Plus size={16} />
                Add Row
              </button>
              <button
                className="btn btn-secondary btn-sm"
                onClick={clearAllTransactions}
                disabled={!transactions.length}
              >
                <Trash2 size={16} />
                Clear All
              </button>
            </div>
          </div>

          {!inputText ? (
            <div className="empty-state">
              <FileSpreadsheet size={48} />
              <h4>No transactions loaded</h4>
              <p>Upload a file or use the example data</p>
            </div>
          ) : (
            <>
              <div className="table-wrapper">
                <table className="transaction-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>
                        <Calendar size={14} /> Date
                      </th>
                      <th>
                        <Coins size={14} /> Coin
                      </th>
                      <th>Type</th>
                      <th>Amount</th>
                      <th>Price (R)</th>
                      <th>
                        <Wallet size={14} /> Wallet
                      </th>
                      <th>Fee Rate</th>
                      <th>Fee Coin</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    {transactions.map((tx, index) => (
                      <tr key={index}>
                        <td className="row-number">{index + 1}</td>
                        <td>
                          <input
                            type="date"
                            value={tx.date}
                            // onChange={(e) =>
                            //   updateTransaction(index, "date", e.target.value)
                            // }
                            className="table-input"
                          />
                        </td>
                        <td>
                          <input
                            type="text"
                            value={tx.coin || tx.from_coin || ""}
                            // onChange={(e) =>
                            //   updateTransaction(index, "coin", e.target.value)
                            // }
                            className="table-input"
                            placeholder="BTC"
                          />
                        </td>
                        <td>
                          <select
                            value={tx.type}
                            // onChange={(e) =>
                            //   updateTransaction(index, "type", e.target.value)
                            // }
                            className="table-select"
                          >
                            <option value={tx.type}>{tx.type.toUpperCase()}</option>
                          </select>
                        </td>
                        <td>
                          <input
                            type="number"
                            step="0.00000001"
                            value={tx.amount}
                            // onChange={(e) =>
                            //   updateTransaction(
                            //     index,
                            //     "amount",
                            //     parseFloat(e.target.value)
                            //   )
                            // }
                            className="table-input"
                          />
                        </td>
                        <td>
                          <input
                            type="number"
                            step="0.01"
                            value={tx.price}
                            // onChange={(e) =>
                            //   updateTransaction(
                            //     index,
                            //     "price",
                            //     parseFloat(e.target.value)
                            //   )
                            // }
                            className="table-input"
                          />
                        </td>
                        <td>
                          <input
                            type="text"
                            value={tx.wallet || " " || "default"}
                            // onChange={(e) =>
                            //   updateTransaction(index, "wallet", e.target.value)
                            // }
                            className="table-input"
                          />
                        </td>
                        <td>
                          <input
                            type="number"
                            step="0.001"
                            value={tx.fee || 0}
                            // onChange={(e) =>
                            //   updateTransaction(
                            //     index,
                            //     "fee",
                            //     parseFloat(e.target.value) || 0
                            //   )
                            // }
                            className="table-input"
                            placeholder="0.00"
                          />
                        </td>
                        <td>
                          <input
                            type="text"
                            value={tx.fee_coin || "ZAR"}
                            // onChange={(e) =>
                            //   updateTransaction(
                            //     index,
                            //     "fee_coin",
                            //     e.target.value
                            //   )
                            // }
                            className="table-input"
                            placeholder="ZAR"
                          />
                        </td>
                        <td>
                          <button
                            className="btn-icon danger"
                            onClick={() => removeTransaction(index)}
                            title="Remove"
                          >
                            <Trash2 size={16} />
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              <div className="table-note">
                <p>
                  <small>
                    Fee: Enter transaction fee as a decimal (e.g., 0.01 = 1%).
                    Fee coin is the currency fee was paid in (usually ZAR).
                  </small>
                </p>
              </div>
            </>
          )}
        </div>

        {inputText && (
          <>
            <div className="calculator-container">
              <CalculatorBtn
                handleCalculate={handleCalculate}
                loading={loading}
              />
            </div>
            <div className="errMsg-container">
              {error && <ErrMsgInputTab error={error} />}
            </div>
          </>
        )}
      </div>
    </div>
  );
};

export default InputTab;
