export interface State {
  dialog: DialogOption;
  modal: ModalOption;
}

export interface DropDown {
  value: number;
  text: string;
  old: boolean;
}

export interface DialogOption {
  modalColor: string | null;
  headerColor: string | null;
  type: 'error' | 'warning' | 'info';
  title: string | null;
  messages: string | string[];
  server: boolean;
}

export interface ModalOption {
  url: string | null;
  width: string | number | null;
  height: string | number | null;
  callback: CallableFunction | null;
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
