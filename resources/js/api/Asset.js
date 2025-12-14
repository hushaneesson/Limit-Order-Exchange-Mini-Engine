import axios from "./axios";

class Asset {
    /**
     * Get all assets
     * @param {Object} params - Optional filtering parameters
     * @returns {Promise} - Axios promise with assets data
     */
    static getAll(params = {}) {
        return axios.get("/mgmt/assets", { params });
    }

    /**
     * Create new turbine
     * @param {Object} turbineData - Turbine data
     * @returns {Promise} - Axios promise with creation result
     */
    static createTurbine(turbineData) {
        return axios.post("/mgmt/assets/turbine", turbineData);
    }

    /**
     * Create new hrsg
     * @param {Object} hrsgData - Hrsg data
     * @returns {Promise} - Axios promise with creation result
     */
    static createHrsg(hrsgData) {
        return axios.post("/mgmt/assets/hrsg", hrsgData);
    }

    /**
     * Update turbine
     * @param {Object} turbineData - Turbine data
     * @returns {Promise} - Axios promise with update result
     */
    static updateTurbine(turbineData) {
        return axios.put(`/mgmt/assets/turbine/${turbineData.id}`, turbineData);
    }

    /**
     * Update hrsg
     * @param {Object} hrsgData
     * @returns {Promise} - Axios promise with update result
     */
    static updateHrsg(hrsgData) {
        return axios.put(`/mgmt/assets/hrsg/${hrsgData.id}`, hrsgData);
    }
}

export default Asset;
