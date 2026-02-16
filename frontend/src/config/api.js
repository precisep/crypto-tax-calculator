// API Configuration
export const API_BASE_URL = 'https://crypto-tax-calculator-production.up.railway.app/api'; //Railway has 30 Trial
//export const API_BASE_URL = 'https://crypto-tax-calculator-vvp4.onrender.com/api'; //Entirely Free Service Just Slow

// Helper function to get auth headers
export const getAuthHeaders = (token) => {
  const headers = {
    'Content-Type': 'application/json',
  };
  
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }
  
  return headers;
};

// Helper function to handle API responses
export const handleApiResponse = async (response) => {
  const responseText = await response.text();
  let data;
  
  try {
    data = JSON.parse(responseText);
  } catch (parseError) {
    throw new Error(`Invalid JSON response from server: ${responseText.substring(0, 100)}...`);
  }
  
  if (!response.ok) {
    throw new Error(data.error || data.message || `Request failed with status ${response.status}`);
  }
  
  return data;
};
