# banchoFragrances
API for helping building perfumes based on ingredients


Aim of this API is to assist a perfume creator hobbyist by suggesting ingredients based on market perfumes compositions.

This API only has 3 calls, all GET calls.

# getting ingredients

Get Ingredients:

GET /ingredients/[id]/?[note_type={BASE|MIDDLE|TOP}]

Answer: 
JSONARRAYOF(
{
"id":INT,
"noteType":STR,
"name":JSONARAYOF( {"fr": STR, "en":STR, etc ....}),
"blendingFactor": FLOAT,
"freq": FLOAT
"adjectives" : JSONARAYOF( {"fr": STR, "en":STR, etc ....})
}
)


