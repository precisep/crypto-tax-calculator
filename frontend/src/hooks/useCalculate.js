// hooks/useCalculate.js
import { useState } from 'react';
import { API_BASE_URL, handleApiResponse } from '../config/api';

export const useCalculate = () => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const calculate = async (transactions) => {
    if (!transactions || transactions.length === 0) {
      setError('No transactions to calculate');
      return { success: false, error: 'No transactions to calculate' };
    }

    setLoading(true);
    setError(null);

    try {
      // Clean and format transactions
      const cleanTransactions = transactions.map(tx => {
        // Format date
        let date = tx.date;
        try {
          const d = new Date(date);
          if (!isNaN(d.getTime())) {
            date = d.toISOString().split('T')[0];
          }
        } catch (e) {
          // Keep original date
        }

        // Build transaction object
        const transactionObj = {
          type: tx.type,
          amount: parseFloat(tx.amount) || 0,
          price: parseFloat(tx.price) || 0,
          date: date,
          coin: tx.coin || '',
          wallet: tx.wallet || 'default',
          fee: parseFloat(tx.fee) || 0,
          fee_coin: tx.fee_coin || 'ZAR',
          description: tx.description || ''
        };

        // Add trade-specific fields
        if (tx.from_coin) transactionObj.from_coin = tx.from_coin;
        if (tx.to_coin) transactionObj.to_coin = tx.to_coin;

        return transactionObj;
      });

      // Log what we're sending to the backend
      console.log('Sending to backend:', cleanTransactions);

      const response = await fetch(`${API_BASE_URL}/calculate-public`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ transactions: cleanTransactions }),
      });

      const data = await handleApiResponse(response);
      console.log('Backend response:', data);

      if (data.success && data.data) {
        // Process results from backend
        const resultsWithDetails = data.data.results.map(result => ({
          ...result,
          showDetails: false,
          // Ensure matched_buys has fee information
          matched_buys: (result.matched_buys || []).map(buy => ({
            ...buy,
            fee: buy.fee || 0,
            fee_rate: buy.fee_rate || 0,
            net_proceeds: buy.net_proceeds || 0
          }))
        }));

        // Calculate total fees from results
        const totalFee = resultsWithDetails.reduce((sum, tx) => sum + (tx.total_fee || 0), 0);

        const formattedResults = {
          ...data.data,
          results: resultsWithDetails,
          totalFee: totalFee,
          taxParameters: data.data.tax_parameters || {
            annual_exclusion: 40000,
            short_term_rate: 18,
            long_term_rate: 10,
            long_term_threshold_years: 3
          }
        };

        return { success: true, data: formattedResults };
      } else {
        throw new Error(data.error || data.message || 'Invalid response format');
      }
    } catch (err) {
      const errorMessage = err.message || 'Calculation failed';
      setError(errorMessage);
      console.error('Calculation error:', err);
      return { success: false, error: errorMessage };
    } finally {
      setLoading(false);
    }
  };

  return { calculate, loading, error };
};