export interface TitleScoreResponse {
  response: TitleScore;
}

export interface Player {
  id: number;
  name: string;
  nameEnglish: string;
  nameOther: null;
  sex: string;
  birthday: string;
  countryId: number,
  countryName: string;
  rankId: number;
  rankName: string;
  isRetired: boolean;
  retired: string;
}

export interface TitleScoreDetail {
  id: number;
  title_score_id: number;
  player_id: number;
  player_name: string;
  division: string;
  player: Player | null;
}

export interface TitleScore {
  id: number;
  countryId: number;
  titleId: number;
  name: string;
  title: string;
  result: string;
  modified: number;
  started: string;
  ended: string;
  isWorld: boolean;
  isOfficial: boolean;
  isSameStarted: boolean;
  winner: Player | null;
  loser: Player | null;
}
