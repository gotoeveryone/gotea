import Vue from 'vue';

// Vue コンポーネントの props で型を利用するための定義
export type Prop<T> = () => T;

export interface Cake {
  csrfToken: string;
  accessUser: string;
}

export interface Window {
  App: Vue;
  Cake: Cake;
}

export interface DropDown {
  value: number;
  text: string;
  old: boolean;
}

export interface DialogOption {
  modalColor: string;
  headerColor: string;
  type: string;
  title: string;
  messages: string | string[];
  server: boolean;
}

export interface ModalOption {
  url: string | number;
  width: string | number;
  height: string;
  callback: Function;
}

export interface Country {
  id: number;
  code: string;
  name: string;
  name_english: string;
  has_title: boolean;
}

export interface Year {
  year: number;
  old: boolean;
}
