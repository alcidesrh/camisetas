import SubmissionError from '../../../error/SubmissionError';
import fetch from '../../../utils/fetch';
import {
  TALLA_CREATE_ERROR,
  TALLA_CREATE_LOADING,
  TALLA_CREATE_SUCCESS,
  TALLA_CREATE_VIOLATIONS,
  TALLA_CREATE_RESET
} from './mutation-types';

const state = {
  loading: false,
  error: '',
  created: null
};

function error(error) {
  return {type: TALLA_CREATE_ERROR, error};
}

function loading(loading) {
  return {type: TALLA_CREATE_LOADING, loading};
}

function success(created) {
  return {type: TALLA_CREATE_SUCCESS, created};
}

function violations(violations) {
  return {type: TALLA_CREATE_VIOLATIONS, violations};
}

function reset() {
  return {type: TALLA_CREATE_RESET};
}

const getters = {
  created: state => state.created,
  error: state => state.error,
  loading: state => state.loading,
  violations: state => state.violations,
};

const actions = {
  create({ commit }, values) {
    commit(loading(true));

    return fetch('/tallas', {method: 'POST', body: JSON.stringify(values)})
      .then(response => {
        commit(loading(false));

        return response.json();
      })
      .then(data => {
        commit(success(data));
      })
      .catch(e => {
        commit(loading(false));

        if (e instanceof SubmissionError) {
          commit(violations(e.errors));
          commit(error(e.errors._error));
          return;
        }

        commit(error(e.message));
      });
  },
  reset({ commit }) {
    commit(reset());
  }
};

const mutations = {
    [TALLA_CREATE_ERROR] (state, payload) {
      state.error = payload.error;
    },
    [TALLA_CREATE_LOADING] (state, payload) {
      state.loading = payload.loading;
    },
    [TALLA_CREATE_SUCCESS] (state, payload) {
      state.created = payload.created;
    },
    [TALLA_CREATE_VIOLATIONS] (state, payload) {
      state.violations = payload.violations;
    },
    [TALLA_CREATE_RESET] (state) {
      state.loading = false;
      state.error = '';
      state.created = null;
      state.violations = null;
    }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
