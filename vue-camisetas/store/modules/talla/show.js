import fetch from '../../../utils/fetch';
import {
  TALLA_SHOW_ERROR,
  TALLA_SHOW_LOADING,
  TALLA_SHOW_RETRIEVED_SUCCESS,
  TALLA_SHOW_RESET
} from './mutation-types';

const state = {
  loading: false,
  error: '',
  retrieved: null
};

function error(error) {
  return {type: TALLA_SHOW_ERROR, error};
}

function loading(loading) {
  return {type: TALLA_SHOW_LOADING, loading};
}

function retrieved(retrieved) {
  return {type: TALLA_SHOW_RETRIEVED_SUCCESS, retrieved};
}

function reset() {
  return {type: TALLA_SHOW_RESET};
}

const getters = {
  error: state => state.error,
  loading: state => state.loading,
  item: state => state.retrieved
};

const actions = {
  retrieve({ commit }, id) {
    commit(loading(true));

    return fetch(id)
      .then(response => response.json())
      .then(data => {
        commit(loading(false));
        commit(retrieved(data));
      })
      .catch(e => {
        commit(loading(false));
        commit(error(e.message));
      });
  },
  reset({ commit }) {
    commit(reset());
  }
};

const mutations = {
    [TALLA_SHOW_ERROR] (state, payload) {
      state.error = payload.error;
    },
    [TALLA_SHOW_LOADING] (state, payload) {
      state.loading = payload.loading;
    },
    [TALLA_SHOW_RETRIEVED_SUCCESS] (state, payload) {
      state.retrieved = payload.retrieved;
    },
    [TALLA_SHOW_RESET] (state) {
      state.retrieved = null;
    }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
