# AI 에이전트로 워드프레스 블록 테마 만들기

기술 노트 "AI 에이전트로 워드프레스 블록 테마 만들기"(발행 예정)의 예제입니다.

Codex(ChatGPT Desktop)에 WordPress Studio MCP와 [Automattic/build-with-wordpress](https://github.com/Automattic/build-with-wordpress) 스킬을 연결하고, 가상의 동네 로스터리 카페 요건으로 블록 테마를 만들었습니다.

## 파일

```
prompt.md     Codex에 준 요건 원문 (가상 카페 '골목볕 로스터리')
theme/        Codex가 만든 블록 테마
```

`theme/`은 커밋을 두 단계로 나눴습니다. 첫 커밋이 Codex가 만든 원본이고, 그 뒤 커밋이 사람이 손본 부분입니다. `git diff`로 비교할 수 있습니다.

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
