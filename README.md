# 💰 Crypto Transaction Calculator

A web-based application that allows users to upload, preview, edit, and calculate cryptocurrency transactions (BUY, SELL, TRADE) with full transparency into the calculation logic.

Built with React & PHP and designed for clarity, accuracy, and user control.

---

## 🚀 Features

### 📥 Excel/CSV to JSON Conversion
- Upload Excel/CSV transaction history
- Automatically converts to structured JSON
- Smart column detection (coin, amount, wallet, date, etc.)
- Handles Excel/CSv date formats correctly

### 🔄 Transaction Types Supported
- BUY
- SELL
- TRADE

Each transaction is normalized into a consistent internal format before calculation.

### 👀 Transaction Preview Table
- Edit transactions before calculation
- Add new rows manually
- Remove individual rows
- Clear all transactions

### 🧮 Calculation Transparency
- Step-by-step breakdown per transaction
- Tax year detection
- Gain/Loss calculations
- Expand individual rows or "Expand All"

### 📊 Clean UI/UX
- Structured layout
- Expandable transaction breakdowns
- Error handling for invalid JSON
- Clear visual feedback for user actions

---

## 🛠 Tech Stack

- **React**
- **JavaScript (ES6+)**
- **PHP Laravel** - backend
- **XLSX (SheetJS)** – Excel parsing
- **Lucide React** – Icons
- Custom CSS – Styling


---

## 📥 Excel Format

Your Excel file should contain columns such as:

| Column Name  | Example      |
|--------------|-------------|
| coin         | BTC         |
| type         | buy         |
| amount       | 0.5         |
| price        | 400000      |
| wallet       | Luno        |
| from_coin    | BTC         |
| to_coin      | ETH         |
| fee          | 0.001       |
| fee_coin     | BTC         |
| date         | 2023-03-15  |

---

## 🧠 How It Works

1. Excel file is read using `XLSX.read`
2. Sheet is converted to JSON
3. Keys are normalized (case-insensitive, flexible matching)
4. Transactions are validated & structured
5. Calculation engine processes transactions sequentially
6. UI displays both results and step-by-step breakdown

---

## ⚙️ Installation

```bash
git clone <repo-url>
cd crypto-transaction-calculator
cd backend/
docker-compose up -d
cd ..
cd frontend
npm i
npm run dev


