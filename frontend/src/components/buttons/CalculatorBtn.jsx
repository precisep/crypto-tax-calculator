import { Calculator } from "lucide-react"

function CalculatorBtn({ handleCalculate, loading }) {
  return (
    <div className="calculator">
        <button 
         className="calculator-btn btn-primary btn-lg"
         onClick={handleCalculate}
         disabled={loading}
        >
        {loading ? (
            <>
                <div className="spinner"></div>
                Calculating...
            </>
            ) : (
            <>
                <Calculator size={15} />
                Calculate
            </>
            )}
        </button>
    </div>
  )
}

export default CalculatorBtn