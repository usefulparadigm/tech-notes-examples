# Codex로 워드프레스 웹사이트 제작하기

기술 노트 "Codex로 워드프레스 웹사이트 제작하기"(발행 예정)의 예제입니다.

Codex(ChatGPT Desktop)에 WordPress Studio MCP와 [Automattic/build-with-wordpress](https://github.com/Automattic/build-with-wordpress) 스킬을 연결하고, 가상의 동네 로스터리 카페 요건으로 블록 테마를 만들었습니다.

## 파일

```
prompt.md                 Codex에 준 요건 원문 (가상 카페 '골목커피')
theme/                    Codex가 만든 블록 테마 (wp-content/themes/golmokcoffee)
content/                  DB에 저장된 콘텐츠의 블록 마크업 (참고용 스냅숏)
  pages/                  홈, 메뉴, 원두, 소개·오시는 길, 소식 페이지
  synced-patterns/        동기화 패턴: 영업시간과 주소, 대표 메뉴, 이번 주 원두, 나머지 메뉴
  navigation-16.html      상단 메뉴
  uploads/                Codex가 만들어 미디어에 올린 원두 이미지 3장
```

테마 파일에는 가게 정보가 거의 없습니다. 영업시간·주소·메뉴·원두는 Codex가 동기화 패턴(DB의 `wp_block`)으로 만들어 여러 페이지에서 불러 씁니다. 그래서 `theme/`만으로는 사이트가 재현되지 않아, DB의 해당 콘텐츠를 `content/`에 따로 남겼습니다.

커밋은 단계별로 나눴습니다. 처음 요청으로 만든 원본, `prompt.md`의 추가 요청(이미지)으로 바뀐 부분, 사람이 손본 부분 순서입니다. `git diff`로 비교할 수 있습니다.

## 실습 환경

(실습 후 기입)

| 항목 | 버전 |
| --- | --- |
| WordPress Studio | |
| WordPress | |
| Codex(ChatGPT Desktop) / 모델 | |
| build-with-wordpress | 커밋 |
| 실습 날짜 | |

## 점검 결과

(실습 후 기입)

| 점검 항목 | 결과 |
| --- | --- |
| theme.json 토큰화 (블록 속성에 색상값·px 직접 사용 여부) | |
| 블록 유효성 (사이트 에디터 "Invalid block"/복구 경고) | |
| 운영 정보 한 곳 관리 (전화번호·영업시간·메뉴) | |
| 왕복 편집 (사이트 에디터에서 고친 뒤 에이전트에게 수정 요청) | |
| 사람 개입 횟수, 걸린 시간 | |

토큰화 점검에 쓴 명령:

```bash
grep -rnE '#[0-9a-fA-F]{3,8}\b|[0-9]+px' theme/templates theme/parts theme/patterns
```

## 사용법

(실습 후 기입: Studio 사이트에 `theme/` 폴더를 `wp-content/themes/`로 복사하고 활성화하는 방법)

## 유의사항

- 실습용 예제입니다. 운영 사이트에 그대로 쓰기보다 블록 테마 구조를 살펴보는 용도로 보세요.
- 가게 이름, 주소, 전화번호, 인스타그램 계정은 모두 가상입니다.
