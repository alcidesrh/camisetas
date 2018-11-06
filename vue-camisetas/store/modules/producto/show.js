import fetch from '../../../utils/fetch';
import {
  PRODUCTO_SHOW_ERROR,
  PRODUCTO_SHOW_LOADING,
  PRODUCTO_SHOW_RETRIEVED_SUCCESS,
  PRODUCTO_SHOW_RESET
} from './mutation-types';

const state = {
  loading: false,
  error: '',
  retrieved: null
};

function error(error) {
  return {type: PRODUCTO_SHOW_ERROR, error};
}

function loading(loading) {
  return {type: PRODUCTO_SHOW_LOADING, loading};
}

function retrieved(retrieved) {
  return {type: PRODUCTO_SHOW_RETRIEVED_SUCCESS, retrieved};
}

function reset() {
  return {type: PRODUCTO_SHOW_RESET};
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
    [PRODUCTO_SHOW_ERROR] (state, payload) {
      state.error = payload.error;
    },
    [PRODUCTO_SHOW_LOADING] (state, payload) {
      state.loading = payload.loading;
    },
    [PRODUCTO_SHOW_RETRIEVED_SUCCESS] (state, payload) {
      state.retrieved = payload.retrieved;
    },
    [PRODUCTO_SHOW_RESET] (state) {
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
